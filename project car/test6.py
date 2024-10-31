from ultralytics import YOLO
import cv2
import easyocr
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import re
import mysql.connector
from datetime import datetime, timedelta

db_connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="Car_Management"
)
db_cursor = db_connection.cursor()

if db_connection.is_connected():
    print("เชื่อมต่อฐานข้อมูลสำเร็จ!")
else:
    print("ไม่สามารถเชื่อมต่อฐานข้อมูลได้")

model = YOLO('best.pt')

reader = easyocr.Reader(['th', 'en'], gpu=False)

font_path = "Sarun'sThangLuang.ttf"
font = ImageFont.truetype(font_path, 24)

def process_division_text(text):
    text = text.replace("เ", "1")
    text = re.sub(r'[^ก-ฮ0-9]', '', text)
    return text

def process_running_number_text(text):
    text = re.sub(r'\D', '', text)
    return text

cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()
    if not ret:
        print("ไม่สามารถเปิดกล้องได้")
        break

    results = model.predict(source=frame, imgsz=224, save=False)
    
    for detection in results[0].boxes:
        class_id = int(detection.cls.item())
        class_name = results[0].names[class_id]
        
        if class_name == 'LPN':
            x_min, y_min, x_max, y_max = map(int, detection.xyxy[0])
            cropped_lpn = frame[y_min:y_max, x_min:x_max]
            
            division_results = model.predict(source=cropped_lpn, imgsz=224, save=False)
            best_division_text = ""
            best_division_confidence = 0.0
            best_running_number_text = ""
            best_running_number_confidence = 0.0
            
            for div_detection in division_results[0].boxes:
                div_class_id = int(div_detection.cls.item())
                div_class_name = division_results[0].names[div_class_id]
                
                x_min_div, y_min_div, x_max_div, y_max_div = map(int, div_detection.xyxy[0])
                division_crop = cropped_lpn[y_min_div:y_max_div, x_min_div:x_max_div]
                
                if div_class_name == 'Division':
                    inner_texts = reader.readtext(division_crop, detail=1)
                    for text_info in inner_texts:
                        text, confidence = text_info[1], text_info[2]
                        processed_text = process_division_text(text)
                        if confidence > best_division_confidence:
                            best_division_confidence = confidence
                            best_division_text = processed_text
                
                elif div_class_name == 'Running_Number':
                    inner_texts = reader.readtext(division_crop, detail=1)
                    for text_info in inner_texts:
                        text, confidence = text_info[1], text_info[2]
                        processed_text = process_running_number_text(text)
                        if confidence > best_running_number_confidence:
                            best_running_number_confidence = confidence
                            best_running_number_text = processed_text

            query = """
                SELECT ID, Province FROM Manage_Cars 
                WHERE Division = %s AND Running_Number = %s
            """
            db_cursor.execute(query, (best_division_text, best_running_number_text))
            result = db_cursor.fetchone()

            pil_img = Image.fromarray(cv2.cvtColor(cropped_lpn, cv2.COLOR_BGR2RGB))
            draw = ImageDraw.Draw(pil_img)
            y_offset = 10
            draw.text((10, y_offset), f"Division: {best_division_text}", font=font, fill=(0, 0, 0))
            y_offset += 30
            draw.text((10, y_offset), f"Running Number: {best_running_number_text}", font=font, fill=(0, 0, 0))
            y_offset += 30
            
            if result:
                province = result[1]
                license_plate = f"{best_division_text} {province} {best_running_number_text}"
                
                check_query = """
                    SELECT Entry_Exit_Time FROM Car_Reports 
                    WHERE License_Plate = %s 
                    ORDER BY Entry_Exit_Time DESC LIMIT 1
                """
                db_cursor.execute(check_query, (license_plate,))
                latest_record = db_cursor.fetchone()
                
                if latest_record:
                    last_entry_time = latest_record[0]
                    time_difference = datetime.now() - last_entry_time

                    if time_difference < timedelta(minutes=1):
                        print("พบหมายเลขทะเบียนซ้ำภายใน 1 นาที รอการตรวจสอบใหม่")
                        continue

                insert_query = """
                    INSERT INTO Car_Reports (Car_ID, License_Plate, Status)
                    VALUES (%s, %s, 'In Garage')
                """
                db_cursor.execute(insert_query, (result[0], license_plate))
                db_connection.commit()

                draw.text((10, y_offset), "Vehicle Found in Database", font=font, fill=(0, 128, 0))
                draw.text((10, y_offset + 30), f"License Plate: {license_plate}", font=font, fill=(0, 0, 128))
            else:
                draw.text((10, y_offset), "Vehicle Not Found", font=font, fill=(255, 0, 0))

            cropped_lpn = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)
            cv2.imshow('LPN Cropped', cropped_lpn)

    result_image = results[0].plot()
    result_image_resized = cv2.resize(result_image, (0, 0), fx=0.5, fy=0.5)
    cv2.imshow('YOLO Result (Small)', result_image_resized)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

db_cursor.close()
db_connection.close()
cap.release()
cv2.destroyAllWindows()
