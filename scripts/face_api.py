from fastapi import FastAPI, UploadFile, File, Form
import uvicorn
import os
import shutil
import base64
from deepface import DeepFace
import json
import logging

app = FastAPI()

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

TEMP_DIR = "temp_faces"
os.makedirs(TEMP_DIR, exist_ok=True)

@app.post("/verify")
async def verify_face(
    captured_image_base64: str = Form(...),
    reference_image_path: str = Form(...)
):
    try:
        # 1. Validate and prep captured image
        if "," in captured_image_base64:
            header, encoded = captured_image_base64.split(",", 1)
        else:
            encoded = captured_image_base64

        # Use a unique filename for concurrent requests if needed, but for local single user, captured.jpg is fine
        captured_path = os.path.join(TEMP_DIR, "captured.jpg")
        
        with open(captured_path, "wb") as f:
            f.write(base64.b64decode(encoded))

        # 2. Check if reference image exists
        if not os.path.exists(reference_image_path):
            return {"verified": False, "error": f"Reference image not found at {reference_image_path}"}

        # 3. Perform verification using DeepFace
        # VGG-Face is usually pre-installed/accessible and reliable
        result = DeepFace.verify(
            img1_path=captured_path,
            img2_path=reference_image_path,
            enforce_detection=True,
            model_name="VGG-Face",
            detector_backend="opencv",
            align=True
        )

        logger.info(f"Verification result: {result['verified']} (Distance: {result['distance']})")

        return {
            "verified": bool(result["verified"]),
            "distance": float(result["distance"]),
            "threshold": float(result["threshold"]),
            "model": result["model"],
            "detector_backend": result["detector_backend"]
        }

    except Exception as e:
        error_msg = str(e)
        logger.error(f"Error during verification: {error_msg}")
        return {
            "verified": False,
            "error": error_msg
        }

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)
