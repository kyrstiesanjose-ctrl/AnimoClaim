import cv2
import time
import requests
import threading
from flask import Flask, Response
from flask_cors import CORS
from ultralytics import solutions

# URL pointing to your local PHP update endpoint
PHP_API_URL = "http://localhost/claim/api/update_traffic.php"
LOCATION_NAME = "John L. Gokongwei Jr. Innovation Center"
MAX_CAPACITY = 30 

app = Flask(__name__)
CORS(app)

# Open camera (0 for webcam or DroidCam index)
cap = cv2.VideoCapture(0)

# Crossing line coordinates for crowd counter
line_points = [(20, 400), (1080, 400)]

# Initialize Ultralytics Object Counter
counter = solutions.ObjectCounter(
    show=False,
    region=line_points,
    model="yolo11n.pt",
    line_width=2,
)

latest_headcount = 0

def post_traffic_loop():
    """ Periodically syncs headcount value to the PHP API """
    global latest_headcount
    while True:
        try:
            payload = {
                "campus": "laguna",
                "location_name": LOCATION_NAME,
                "current_headcount": latest_headcount,
                "max_capacity": MAX_CAPACITY
            }
            res = requests.post(PHP_API_URL, json=payload, timeout=3)
            print(f"[Sync] Live Count: {latest_headcount} -> PHP Status: {res.status_code}")
        except Exception as e:
            print(f"[Sync Error] Ensure Apache/XAMPP is running: {e}")
        time.sleep(2)

# Start background sync thread
threading.Thread(target=post_traffic_loop, daemon=True).start()

def generate_mjpeg_stream():
    global latest_headcount
    while cap.isOpened():
        success, frame = cap.read()
        if not success:
            break

        results = counter(frame)

        # Count objects in current frame
        if hasattr(counter, 'in_count') and hasattr(counter, 'out_count'):
            latest_headcount = abs(counter.in_count - counter.out_count)
        else:
            boxes = results[0].boxes
            latest_headcount = sum(1 for box in boxes if int(box.cls[0]) == 0)

        annotated_frame = results.plot_im
        ret, buffer = cv2.imencode('.jpg', annotated_frame)
        if not ret:
            continue

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + buffer.tobytes() + b'\r\n')

@app.route('/video_feed')
def video_feed():
    return Response(generate_mjpeg_stream(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    print("Starting YOLO Bridge & Flask Server on Port 5000...")
    app.run(host='0.0.0.0', port=5000, debug=False)