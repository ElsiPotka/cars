
import os
import time
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin

# List of brands matching the Seeder
BRANDS = [
    'Acura', 'Alfa Romeo', 'Aston Martin', 'Audi', 'Bentley', 'BMW', 'Bugatti', 'Buick',
    'BYD', 'Cadillac', 'Chevrolet', 'Chrysler', 'Citroën', 'Dacia', 'Dodge', 'Ferrari',
    'Fiat', 'Ford', 'Genesis', 'GMC', 'Honda', 'Hyundai', 'Infiniti', 'Jaguar', 'Jeep',
    'Kia', 'Koenigsegg', 'Lamborghini', 'Land Rover', 'Lexus', 'Lincoln', 'Lotus', 'Maserati',
    'Mazda', 'McLaren', 'Mercedes-Benz', 'Mini', 'Mitsubishi', 'Nissan', 'Opel', 'Pagani',
    'Peugeot', 'Porsche', 'Ram', 'Renault', 'Rolls-Royce', 'Seat', 'Skoda', 'Smart',
    'Subaru', 'Suzuki', 'Tesla', 'Toyota', 'Volkswagen', 'Volvo'
]

# Target Directory
TARGET_DIR = os.path.join(os.getcwd(), 'storage/app/public/brands')
os.makedirs(TARGET_DIR, exist_ok=True)

def get_wikipedia_url(brand_name):
    # Handle special cases for Wikipedia URLs
    if brand_name == 'Tesla':
        return 'https://en.wikipedia.org/wiki/Tesla,_Inc.'
    if brand_name == 'Jaguar':
        return 'https://en.wikipedia.org/wiki/Jaguar_Cars'
    if brand_name == 'Mini':
        return 'https://en.wikipedia.org/wiki/Mini_(marque)'
    if brand_name == 'Smart':
        return 'https://en.wikipedia.org/wiki/Smart_(marque)'
    if brand_name == 'Ram':
        return 'https://en.wikipedia.org/wiki/Ram_Trucks'
    if brand_name == 'Genesis':
        return 'https://en.wikipedia.org/wiki/Genesis_Motor'
    if brand_name == 'Infiniti':
        return 'https://en.wikipedia.org/wiki/Infiniti'
    if brand_name == 'Lincoln':
        return 'https://en.wikipedia.org/wiki/Lincoln_Motor_Company'
    
    # Default
    return f"https://en.wikipedia.org/wiki/{brand_name.replace(' ', '_')}"

def download_logo(brand_name):
    slug = brand_name.lower().replace(' ', '_').replace('-', '_') # Match seeder Logic
    filename = f"{slug}.png"
    filepath = os.path.join(TARGET_DIR, filename)

    if os.path.exists(filepath):
        print(f"[-] {brand_name}: already exists.")
        return

    url = get_wikipedia_url(brand_name)
    print(f"[*] {brand_name}: Searching {url}...")
    
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }

    try:
        r = requests.get(url, headers=headers, timeout=10)
        if r.status_code != 200:
            print(f"[!] {brand_name}: Failed to fetch page (Status {r.status_code})")
            return

        soup = BeautifulSoup(r.content, 'html.parser')
        
        infobox = soup.find(class_='infobox')
        if not infobox:
            print(f"[!] {brand_name}: No infobox found.")
            return

        img_tag = infobox.find('img')
        if not img_tag:
             img_tag = infobox.find('img', alt=lambda x: x and 'logo' in x.lower())

        if not img_tag:
            print(f"[!] {brand_name}: No logo image found in infobox.")
            return

        img_url = img_tag.get('src')
        if not img_url:
            print(f"[!] {brand_name}: Image tag has no src.")
            return

        if img_url.startswith('//'):
            img_url = 'https:' + img_url

        print(f"[*] {brand_name}: Found image {img_url}")

        img_r = requests.get(img_url, headers=headers, timeout=10)
        if img_r.status_code == 200:
            with open(filepath, 'wb') as f:
                f.write(img_r.content)
            print(f"[+] {brand_name}: Saved to {filename}")
        else:
            print(f"[!] {brand_name}: Failed to download image.")

    except Exception as e:
        print(f"[!] {brand_name}: Error - {e}")

    # Be nice to Wikipedia
    time.sleep(1)

def main():
    print(f"Starting logo download to {TARGET_DIR}")
    for brand in BRANDS:
        download_logo(brand)
    print("Done.")

if __name__ == '__main__':
    main()
