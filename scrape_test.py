import urllib.request
import urllib.parse
from bs4 import BeautifulSoup
import re

req = urllib.request.Request('https://sieuthivattu.com/', headers={'User-Agent': 'Mozilla/5.0'})
with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')
    soup = BeautifulSoup(html, 'html.parser')
    
    sections = soup.select('.section_product_2')  # Haravan typically uses section classes
    if not sections:
        sections = soup.select('.home-section')
    if not sections:
        sections = soup.select('.block-product')
    
    with open('output_sections.txt', 'w', encoding='utf-8') as f:
        # Just grab the titles of all sections to see how groups are structured
        headings = soup.find_all(['h2', 'h3'])
        for h in headings:
            f.write(h.text.strip() + '\n')
            
        f.write("\n==================================================\n")
        # Try to find the section wrapper of our Vicem product
        products = soup.find_all(string=re.compile("Vicem"))
        for p in products:
            parent = p.parent
            while parent and parent.name != 'body':
                if 'class' in parent.attrs:
                    f.write(parent.name + ' ' + str(parent['class']) + '\n')
                parent = parent.parent
            break
