import cv2
import time
import requests
import threading
from flask import Flask, Response
from flask_cors import CORS
from ultralytics import YOLO

# Configuration
PHP_API_URL = "http://localhost/claim/api/update_traffic.php"
LOCATION_NAME = "John L. Gokongwei Jr. Innovation Center"
MAX_CAPACITY = 30 

app = Flask(__name__)
CORS(app)

print("Loading YOLO model...")
model = YOLO("yolo11n.pt") 

# Open camera using local device index 2
cap = cv2.VideoCapture(2)

# Global variables shared across threads
latest_headcount = 0
current_frame_bytes = None

def detection_loop():
    """ Runs constantly in the background, processing camera frames with YOLO """
    global latest_headcount, current_frame_bytes
    
    print("[YOLO] Detection camera loop started...")
    while cap.isOpened():
        success, frame = cap.read()
        if not success:
            time.sleep(0.1)
            continue

        # 1. Run YOLO, filtering for persons (class 0)
        results = model(frame, classes=[0], verbose=False)
        
        # 2. Update the global headcount
        latest_headcount = len(results[0].boxes)

        # 3. Draw boxes and encode the frame for the web stream
        annotated_frame = results[0].plot()
        ret, buffer = cv2.imencode('.jpg', annotated_frame)
        if ret:
            current_frame_bytes = buffer.tobytes()

# Start the detection loop in its own thread IMMEDIATELY
threading.Thread(target=detection_loop, daemon=True).start()

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
            print(f"[Sync Error] Check XAMPP/Apache: {e}")
        time.sleep(2)

# Start background sync thread
threading.Thread(target=post_traffic_loop, daemon=True).start()

def generate_mjpeg_stream():
    """ Yields the latest processed frame to the web browser """
    global current_frame_bytes
    while True:
        if current_frame_bytes is not None:
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + current_frame_bytes + b'\r\n')
        time.sleep(0.05) # Limit to ~20 FPS to save browser memory

@app.route('/video_feed')
def video_feed():
    return Response(generate_mjpeg_stream(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    print("Starting YOLO Bridge & Flask Server on Port 5000...")
    # threaded=True allows multiple browser connections without freezing
    app.run(host='0.0.0.0', port=5000, debug=False, threaded=True)