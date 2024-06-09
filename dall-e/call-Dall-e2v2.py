import requests
import json

# Get user inputs
api_key = input("Su calve API: ")
user = input("Su usuraio orientativo: ")
prompt = input("Prompt: ")
size = input("1024x1024 o 1024x1792 o 1024x1792: ")
num_images = int(input("Cantidad de imagenes 1-10: "))
output_filename = input("Nombre base de archivo: ")

# Set up the API endpoint and parameters
url = "https://api.openai.com/v1/images/generations"
model = "dall-e-3"

# Define the HTTP headers and request payload
headers = {
    "Content-Type": "application/json",
    "Authorization": f"Bearer {api_key}"
}
payload = {
    "model": model,
    "prompt": prompt,
    "num_images": num_images,
    "size": size,
    "user": user
}

# Send the HTTP request to the API endpoint
response = requests.post(url, headers=headers, data=json.dumps(payload))

# Get the response data as a JSON object
response_data = response.json()

# Loop through the generated images and download them
for i in range(num_images):
    # Get the image URL from the response data
    image_url = response_data["data"][i]["url"]

    # Download the image file
    response = requests.get(image_url)
    with open(f"{output_filename}-{i+1}.png", "wb") as f:
        f.write(response.content)
