import os
import requests

# Dictionary of car names (from database) mapped to Wikimedia image URLs
# Using Wikimedia Commons images which are generally safe to use
car_images = {
    'Avanza 1.3 G': 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/2022_Toyota_Avanza_1.3_E_%28Indonesia%29_front_view.jpg/800px-2022_Toyota_Avanza_1.3_E_%28Indonesia%29_front_view.jpg',
    'Xenia 1.3 R': 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/2022_Daihatsu_Xenia_1.5_R_ADS_%28Indonesia%29_front_view.jpg/800px-2022_Daihatsu_Xenia_1.5_R_ADS_%28Indonesia%29_front_view.jpg',
    'Innova Reborn 2.4 G': 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/07/2019_Toyota_Kijang_Innova_2.4_V_wagon_%28GUN142R%3B_10-21-2022%29%2C_South_Tangerang.jpg/800px-2019_Toyota_Kijang_Innova_2.4_V_wagon_%28GUN142R%3B_10-21-2022%29%2C_South_Tangerang.jpg',
    'Brio Satya E': 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/2023_Honda_Brio_Satya_E_CVT_%28Indonesia%29_front_view.jpg/800px-2023_Honda_Brio_Satya_E_CVT_%28Indonesia%29_front_view.jpg',
    'Jazz RS': 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/2018_Honda_Jazz_RS_%28Indonesia%29_front_view.jpg/800px-2018_Honda_Jazz_RS_%28Indonesia%29_front_view.jpg',
    'Ertiga GX': 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/2022_Suzuki_Ertiga_GX_Hybrid.jpg/800px-2022_Suzuki_Ertiga_GX_Hybrid.jpg',
    'Fortuner 2.4 VRZ': 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/2016_Toyota_Fortuner_2.4_VRZ_wagon_%28GUN165R%3B_02-27-2022%29%2C_South_Tangerang.jpg/800px-2016_Toyota_Fortuner_2.4_VRZ_wagon_%28GUN165R%3B_02-27-2022%29%2C_South_Tangerang.jpg', 
    'Pajero Sport Dakar': 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/2021_Mitsubishi_Pajero_Sport_Dakar_Ultimate_4x2_%28Indonesia%29_front_view.jpg/800px-2021_Mitsubishi_Pajero_Sport_Dakar_Ultimate_4x2_%28Indonesia%29_front_view.jpg'
}

upload_dir = 'uploads'
if not os.path.exists(upload_dir):
    os.makedirs(upload_dir)

print("Starting download...")

for car_name, url in car_images.items():
    safe_name = car_name.split()[0].lower() # avanza, xenia, etc.
    filename = f"{safe_name}.jpg" # Keeping as jpg for now to avoid conversion issues, browser handles it fine
    filepath = os.path.join(upload_dir, filename)
    
    print(f"Downloading {car_name} to {filename}...")
    try:
        response = requests.get(url, timeout=10)
        if response.status_code == 200:
            with open(filepath, 'wb') as f:
                f.write(response.content)
            print(f"Success: {filename}")
        else:
            print(f"Failed to download {car_name}: Status {response.status_code}")
    except Exception as e:
        print(f"Error downloading {car_name}: {e}")

print("Download complete.")
