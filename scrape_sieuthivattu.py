import urllib.request
from bs4 import BeautifulSoup
import json
import random

def scrape_data():
    url = 'https://sieuthivattu.com/'
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    
    with urllib.request.urlopen(req) as response:
        html = response.read().decode('utf-8')
        soup = BeautifulSoup(html, 'html.parser')
        
        data = []
        
        # Look for the product groups wrapper
        # Often it's a section or div with class 'product_group'
        groups = soup.find_all('div', class_='product_group')
        for group in groups:
            group_data = {}
            # Try to get the group name (h2 or h3)
            heading = group.find(['h2', 'h3'])
            if not heading:
                continue
                
            group_name = heading.get_text(strip=True)
            if not group_name:
                continue
            
            group_data['group_name'] = group_name
            group_data['products'] = []
            
            # Now find all products in this group
            products = group.find_all('div', class_='product')
            for prod in products:
                title_elem = prod.find('div', class_='product-title')
                if not title_elem:
                    continue
                
                name_elem = title_elem.find('a')
                if not name_elem:
                    continue
                
                name = name_elem.get_text(strip=True)
                
                price_elem = prod.find('div', class_='product-price')
                price = "Liên hệ"
                if price_elem:
                    ins = price_elem.find('ins')
                    if ins:
                        price = ins.get_text(strip=True)
                    else:
                        price = price_elem.get_text(strip=True)
                        
                # User wants to auto-add price if it's "Liên hệ"
                if "Liên hệ" in price or not price:
                    # Auto add a mock price
                    mock_price = random.randint(50, 500) * 1000
                    price = f"{mock_price:,.0f}₫".replace(',', '.')
                    
                group_data['products'].append({
                    'name': name,
                    'price': price
                })
                
            if group_data['products']:
                data.append(group_data)
                
        return data

if __name__ == '__main__':
    data = scrape_data()
    with open('sieuthivattu_data.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
    print("Scraping finished. Data saved to sieuthivattu_data.json")
