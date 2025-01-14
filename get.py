import requests

def get_public_ip():
    try:
        # Make a request to the ipify API
        response = requests.get('https://api.ipify.org?format=json')
        response.raise_for_status()  # Raise an HTTPError for bad responses
        data = response.json()
        return data['ip']
    except requests.RequestException as e:
        print(f"Error fetching public IP: {e}")
        return None

# Get and print the public IP address
public_ip = get_public_ip()
if public_ip:
    print(f"Your public IP address is: {public_ip}")
