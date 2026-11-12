import csv
import random
from datetime import date, timedelta

# Daftar 95 Produk Sembako Autentik Indonesia
PRODUCTS = [
    # 1. Beras & Tepung (12 produk)
    {"id": 1, "nama": "Beras Rojolele 5kg", "kategori": "Beras & Tepung", "harga": 72000},
    {"id": 2, "nama": "Beras Pandan Wangi 5kg", "kategori": "Beras & Tepung", "harga": 78000},
    {"id": 3, "nama": "Beras Setra Ramos 5kg", "kategori": "Beras & Tepung", "harga": 69000},
    {"id": 4, "nama": "Beras Sania 5kg", "kategori": "Beras & Tepung", "harga": 71000},
    {"id": 5, "nama": "Beras Ketan Putih 1kg", "kategori": "Beras & Tepung", "harga": 18000},
    {"id": 6, "nama": "Tepung Terigu Segitiga Biru 1kg", "kategori": "Beras & Tepung", "harga": 12500},
    {"id": 7, "nama": "Tepung Terigu Cakra Kembar 1kg", "kategori": "Beras & Tepung", "harga": 14000},
    {"id": 8, "nama": "Tepung Terigu Kunci Biru 1kg", "kategori": "Beras & Tepung", "harga": 13000},
    {"id": 9, "nama": "Tepung Beras Rose Brand 500g", "kategori": "Beras & Tepung", "harga": 8500},
    {"id": 10, "nama": "Tepung Ketan Rose Brand 500g", "kategori": "Beras & Tepung", "harga": 9500},
    {"id": 11, "nama": "Tepung Maizena Maizenaku 150g", "kategori": "Beras & Tepung", "harga": 6000},
    {"id": 12, "nama": "Tepung Tapioka Gunung Agung 500g", "kategori": "Beras & Tepung", "harga": 7500},

    # 2. Minyak Goreng & Margarin (10 produk)
    {"id": 13, "nama": "Minyak Goreng Bimoli 2L", "kategori": "Minyak Goreng & Margarin", "harga": 37000},
    {"id": 14, "nama": "Minyak Goreng Bimoli 1L", "kategori": "Minyak Goreng & Margarin", "harga": 19500},
    {"id": 15, "nama": "Minyak Goreng Filma 2L", "kategori": "Minyak Goreng & Margarin", "harga": 38000},
    {"id": 16, "nama": "Minyak Goreng Sania 2L", "kategori": "Minyak Goreng & Margarin", "harga": 36500},
    {"id": 17, "nama": "Minyak Goreng Fortune 2L", "kategori": "Minyak Goreng & Margarin", "harga": 35500},
    {"id": 18, "nama": "Minyak Goreng SunCo 2L", "kategori": "Minyak Goreng & Margarin", "harga": 37500},
    {"id": 19, "nama": "Minyak Goreng Kunci Mas 1L", "kategori": "Minyak Goreng & Margarin", "harga": 18500},
    {"id": 20, "nama": "Margarin Blue Band 200g", "kategori": "Minyak Goreng & Margarin", "harga": 10500},
    {"id": 21, "nama": "Margarin ForVITA 200g", "kategori": "Minyak Goreng & Margarin", "harga": 7500},
    {"id": 22, "nama": "Minyak Kelapa Barco 1L", "kategori": "Minyak Goreng & Margarin", "harga": 32000},

    # 3. Gula & Pemanis (8 produk)
    {"id": 23, "nama": "Gula Pasir Gulaku Tebu Kuning 1kg", "kategori": "Gula & Pemanis", "harga": 17500},
    {"id": 24, "nama": "Gula Pasir Gulaku Putih 1kg", "kategori": "Gula & Pemanis", "harga": 18000},
    {"id": 25, "nama": "Gula Pasir Rose Brand 1kg", "kategori": "Gula & Pemanis", "harga": 17000},
    {"id": 26, "nama": "Gula Merah Aren 500g", "kategori": "Gula & Pemanis", "harga": 16000},
    {"id": 27, "nama": "Gula Merah Kelapa 500g", "kategori": "Gula & Pemanis", "harga": 14000},
    {"id": 28, "nama": "Gula Batu Putih 250g", "kategori": "Gula & Pemanis", "harga": 8000},
    {"id": 29, "nama": "Madu TJ Murni 150g", "kategori": "Gula & Pemanis", "harga": 22000},
    {"id": 30, "nama": "Pemanis Tropicana Slim Sweetener 50 sachet", "kategori": "Gula & Pemanis", "harga": 36000},

    # 4. Bumbu Dapur & Penyedap (15 produk)
    {"id": 31, "nama": "Garam Dapur Beryodium Cap Kapal 250g", "kategori": "Bumbu Dapur & Penyedap", "harga": 3500},
    {"id": 32, "nama": "Garam Dapur Halus Refina 500g", "kategori": "Bumbu Dapur & Penyedap", "harga": 6500},
    {"id": 33, "nama": "Bumbu Kaldu Royco Ayam 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5000},
    {"id": 34, "nama": "Bumbu Kaldu Royco Sapi 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5000},
    {"id": 35, "nama": "Bumbu Kaldu Masako Ayam 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5000},
    {"id": 36, "nama": "Bumbu Kaldu Masako Sapi 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5000},
    {"id": 37, "nama": "Penyedap Rasa Ajinomoto 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5500},
    {"id": 38, "nama": "Penyedap Rasa Sasa 100g", "kategori": "Bumbu Dapur & Penyedap", "harga": 5500},
    {"id": 39, "nama": "Merica Bubuk Ladaku 1 renteng", "kategori": "Bumbu Dapur & Penyedap", "harga": 11000},
    {"id": 40, "nama": "Ketumbar Bubuk Desaku 1 sachet", "kategori": "Bumbu Dapur & Penyedap", "harga": 1500},
    {"id": 41, "nama": "Kunyit Bubuk Desaku 1 sachet", "kategori": "Bumbu Dapur & Penyedap", "harga": 1500},
    {"id": 42, "nama": "Bumbu Racik Indofood Sayur Asem 1 sachet", "kategori": "Bumbu Dapur & Penyedap", "harga": 2500},
    {"id": 43, "nama": "Bumbu Racik Indofood Ayam Goreng 1 sachet", "kategori": "Bumbu Dapur & Penyedap", "harga": 2500},
    {"id": 44, "nama": "Bumbu Racik Indofood Ikan Goreng 1 sachet", "kategori": "Bumbu Dapur & Penyedap", "harga": 2500},
    {"id": 45, "nama": "Terasi Udang ABC 1 renteng", "kategori": "Bumbu Dapur & Penyedap", "harga": 7000},

    # 5. Kecap, Saus, & Sambal (9 produk)
    {"id": 46, "nama": "Kecap Manis Bango 520ml", "kategori": "Kecap, Saus & Sambal", "harga": 24500},
    {"id": 47, "nama": "Kecap Manis ABC 520ml", "kategori": "Kecap, Saus & Sambal", "harga": 21000},
    {"id": 48, "nama": "Kecap Manis Sedaap 550ml", "kategori": "Kecap, Saus & Sambal", "harga": 20000},
    {"id": 49, "nama": "Kecap Asin ABC 135ml", "kategori": "Kecap, Saus & Sambal", "harga": 6500},
    {"id": 50, "nama": "Saus Sambal ABC Extra Pedas 335ml", "kategori": "Kecap, Saus & Sambal", "harga": 15000},
    {"id": 51, "nama": "Saus Sambal Indofood Pedas 335ml", "kategori": "Kecap, Saus & Sambal", "harga": 14500},
    {"id": 52, "nama": "Saus Tomat ABC 335ml", "kategori": "Kecap, Saus & Sambal", "harga": 13000},
    {"id": 53, "nama": "Saus Tiram Saori 133ml", "kategori": "Kecap, Saus & Sambal", "harga": 11500},
    {"id": 54, "nama": "Minyak Wijen Saori 133ml", "kategori": "Kecap, Saus & Sambal", "harga": 16000},

    # 6. Santan & Bahan Olahan (5 produk)
    {"id": 55, "nama": "Santan Kelapa Kara 65ml", "kategori": "Santan & Olahan", "harga": 3500},
    {"id": 56, "nama": "Santan Kelapa Kara 200ml", "kategori": "Santan & Olahan", "harga": 9500},
    {"id": 57, "nama": "Santan Bubuk Sasa 50g", "kategori": "Santan & Olahan", "harga": 4000},
    {"id": 58, "nama": "Pasta Vanila Koepoe Koepoe 25ml", "kategori": "Santan & Olahan", "harga": 6500},
    {"id": 59, "nama": "Baking Powder Koepoe Koepoe 45g", "kategori": "Santan & Olahan", "harga": 7000},

    # 7. Telur & Lauk Praktis (5 produk)
    {"id": 60, "nama": "Telur Ayam Negeri 1kg", "kategori": "Telur & Lauk Praktis", "harga": 28000},
    {"id": 61, "nama": "Telur Ayam Kampung 1 butir", "kategori": "Telur & Lauk Praktis", "harga": 3000},
    {"id": 62, "nama": "Telur Bebek Asin 1 butir", "kategori": "Telur & Lauk Praktis", "harga": 4500},
    {"id": 63, "nama": "Kornet Daging Sapi Pronas 198g", "kategori": "Telur & Lauk Praktis", "harga": 23000},
    {"id": 64, "nama": "Sarden ABC Saus Cabai 155g", "kategori": "Telur & Lauk Praktis", "harga": 11500},

    # 8. Mie Instan & Pasta (12 produk)
    {"id": 65, "nama": "Indomie Mi Goreng Spesial 85g", "kategori": "Mie Instan & Olahan", "harga": 3100},
    {"id": 66, "nama": "Indomie Kuah Rasa Ayam Bawang 69g", "kategori": "Mie Instan & Olahan", "harga": 3100},
    {"id": 67, "nama": "Indomie Kuah Rasa Soto Mie 70g", "kategori": "Mie Instan & Olahan", "harga": 3100},
    {"id": 68, "nama": "Indomie Kuah Rasa Kari Ayam 72g", "kategori": "Mie Instan & Olahan", "harga": 3300},
    {"id": 69, "nama": "Mie Sedaap Goreng 90g", "kategori": "Mie Instan & Olahan", "harga": 3100},
    {"id": 70, "nama": "Mie Sedaap Soto 75g", "kategori": "Mie Instan & Olahan", "harga": 3100},
    {"id": 71, "nama": "Sarimi Isi 2 Rasa Goreng Pedas 126g", "kategori": "Mie Instan & Olahan", "harga": 4200},
    {"id": 72, "nama": "Bihun Instan Kuah Soto Rasa Bihunku 55g", "kategori": "Mie Instan & Olahan", "harga": 4000},
    {"id": 73, "nama": "Mie Telur Cap 3 Ayam 200g", "kategori": "Mie Instan & Olahan", "harga": 6500},
    {"id": 74, "nama": "Sohun Kaca Rusa 100g", "kategori": "Mie Instan & Olahan", "harga": 5500},
    {"id": 75, "nama": "Makaroni Sedani 1kg", "kategori": "Mie Instan & Olahan", "harga": 20000},
    {"id": 76, "nama": "Spaghetti La Fonte 225g", "kategori": "Mie Instan & Olahan", "harga": 10500},

    # 9. Kopi, Teh, & Susu (10 produk)
    {"id": 77, "nama": "Kopi Bubuk Kapal Api Spesial Mix 1 renteng", "kategori": "Kopi, Teh & Susu", "harga": 13500},
    {"id": 78, "nama": "Kopi Nescafe Classic 100g", "kategori": "Kopi, Teh & Susu", "harga": 38000},
    {"id": 79, "nama": "Kopi Torabika Duo 1 renteng", "kategori": "Kopi, Teh & Susu", "harga": 12000},
    {"id": 80, "nama": "Kopi Good Day Cappuccino 1 renteng", "kategori": "Kopi, Teh & Susu", "harga": 19500},
    {"id": 81, "nama": "Teh Celup SariWangi 25 sachet", "kategori": "Kopi, Teh & Susu", "harga": 7000},
    {"id": 82, "nama": "Teh Celup Sosro 25 sachet", "kategori": "Kopi, Teh & Susu", "harga": 6500},
    {"id": 83, "nama": "Teh Bubuk Cap Botol 40g", "kategori": "Kopi, Teh & Susu", "harga": 4000},
    {"id": 84, "nama": "Susu Kental Manis Frisian Flag Cokelat 370g", "kategori": "Kopi, Teh & Susu", "harga": 12500},
    {"id": 85, "nama": "Susu Kental Manis Frisian Flag Putih 370g", "kategori": "Kopi, Teh & Susu", "harga": 12500},
    {"id": 86, "nama": "Susu Kental Manis Indomilk Putih 370g", "kategori": "Kopi, Teh & Susu", "harga": 12000},

    # 10. Kebutuhan Rumah Tangga & Cuci (9 produk)
    {"id": 87, "nama": "Sabun Cuci Piring Sunlight Jeruk Nipis 650ml", "kategori": "Kebersihan Rumah Tangga", "harga": 14500},
    {"id": 88, "nama": "Sabun Cuci Piring Mama Lemon 680ml", "kategori": "Kebersihan Rumah Tangga", "harga": 13500},
    {"id": 89, "nama": "Deterjen Bubuk Rinso Anti Noda 770g", "kategori": "Kebersihan Rumah Tangga", "harga": 21000},
    {"id": 90, "nama": "Deterjen Bubuk Daia Bunga 850g", "kategori": "Kebersihan Rumah Tangga", "harga": 18000},
    {"id": 91, "nama": "Pembersih Lantai Wipol Karbol 750ml", "kategori": "Kebersihan Rumah Tangga", "harga": 16500},
    {"id": 92, "nama": "Sabun Mandi Lifebuoy Total 10 110g", "kategori": "Kebersihan Rumah Tangga", "harga": 4500},
    {"id": 93, "nama": "Pasta Gigi Pepsodent Pencegah Gigi Berlubang 190g", "kategori": "Kebersihan Rumah Tangga", "harga": 13500},
    {"id": 94, "nama": "Sampo Pantene Anti Dandruff 160ml", "kategori": "Kebersihan Rumah Tangga", "harga": 24000},
    {"id": 95, "nama": "Obat Nyamuk Bakar Baygon 1 box", "kategori": "Kebersihan Rumah Tangga", "harga": 6000},
]

assert len(PRODUCTS) == 95, f"Expected 95 products, got {len(PRODUCTS)}"
BY_ID = {p["id"]: p for p in PRODUCTS}

# Pola Pembelian Asosiasi Konsumen Sembako (Consumer Basket Affinity Patterns)
AFFINITY_PATTERNS = [
    # Pola 1: Kebutuhan Pokok Sembako (Beras + Minyak + Gula + Telur)
    {
        "core": [1, 13, 23, 60], # Beras Rojolele, Minyak Bimoli 2L, Gulaku Kuning, Telur Ayam
        "variants": [2, 3, 14, 15, 16, 24, 25],
        "addons": [31, 33, 46, 81] # Garam, Royco, Bango, Sariwangi
    },
    # Pola 2: Paket Masak Gorengan (Tepung + Minyak + Garam + Royco)
    {
        "core": [6, 13, 31, 33], # Segitiga Biru, Bimoli 2L, Garam Kapal, Royco Ayam
        "variants": [7, 8, 9, 15, 16, 20, 21],
        "addons": [39, 43, 60] # Ladaku, Racik Ayam, Telur
    },
    # Pola 3: Makanan Cepat Saji (Mie Instan + Telur + Saus Sambal + Kecap)
    {
        "core": [65, 60, 50, 46], # Indomie Goreng, Telur Ayam, Saus ABC, Kecap Bango
        "variants": [66, 67, 68, 69, 70, 47, 48, 51],
        "addons": [63, 64, 62] # Kornet, Sarden, Telur Asin
    },
    # Pola 4: Paket Sarapan & Minuman Pagi (Kopi + Gula + Teh + Susu Kental)
    {
        "core": [77, 23, 81, 84], # Kopi Kapal Api, Gulaku Kuning, Sariwangi, Frisian Flag Cokelat
        "variants": [78, 79, 80, 24, 25, 82, 85, 86],
        "addons": [20, 6] # Margarin, Tepung
    },
    # Pola 5: Paket Masakan Santan Dapur (Santan Kara + Bumbu Racik + Minyak + Ketumbar)
    {
        "core": [55, 42, 13, 40], # Santan Kara, Bumbu Racik Sayur Asem, Bimoli, Ketumbar Desaku
        "variants": [56, 57, 43, 44, 14, 15],
        "addons": [41, 45, 26, 39] # Kunyit, Terasi, Gula Aren, Ladaku
    },
    # Pola 6: Paket Kebersihan Rumah Tangga (Sunlight + Rinso + Wipol + Sabun Mandi)
    {
        "core": [87, 89, 91, 92], # Sunlight, Rinso, Wipol, Lifebuoy
        "variants": [88, 90],
        "addons": [93, 94, 95] # Pepsodent, Pantene, Baygon
    },
    # Pola 7: Mie Kuah & Telur Hangat (Mie Kuah + Telur + Kecap Asin + Saus)
    {
        "core": [66, 60, 49, 50], # Indomie Ayam Bawang, Telur Ayam, Kecap Asin, Saus Sambal
        "variants": [67, 68, 70, 71, 72, 73],
        "addons": [39, 45, 64] # Ladaku, Terasi, Sarden
    }
]

def generate_transactions(output_csv):
    random.seed(2027) # Seed konsisten tahun 2027
    
    # 3 Bulan Terakhir Tahun 2027 terhitung bulan saat ini (November):
    # September (01-30), Oktober (01-31), November (01-30) tahun 2027
    start_date = date(2027, 9, 1)
    end_date = date(2027, 11, 30)
    
    rows = []
    trx_counter = 1
    
    # Pelacak frekuensi agar seluruh 95 produk pasti ada transaksi
    product_usage_count = {p["id"]: 0 for p in PRODUCTS}

    current_date = start_date
    while current_date <= end_date:
        date_str = current_date.strftime("%Y-%m-%d")
        
        # 7 hingga 11 transaksi per hari di toko sembako
        day_of_week = current_date.weekday()
        if day_of_week in (4, 5, 6): # Jumat, Sabtu, Minggu lebih ramai
            num_trx = random.randint(8, 12)
        else:
            num_trx = random.randint(6, 9)
            
        for _ in range(num_trx):
            trx_code = f"TRX-{current_date.strftime('%Y%m%d')}-{trx_counter:04d}"
            trx_counter += 1
            
            rand_val = random.random()
            selected_product_ids = set()
            
            if rand_val < 0.72:
                # 72% belanja terarah pada pola afinitas belanja
                pattern = random.choice(AFFINITY_PATTERNS)
                core_picks = random.sample(pattern["core"], random.randint(2, min(3, len(pattern["core"]))))
                for cid in core_picks:
                    selected_product_ids.add(cid)
                    
                if random.random() < 0.50 and pattern["variants"]:
                    selected_product_ids.add(random.choice(pattern["variants"]))
                    
                if random.random() < 0.45 and pattern["addons"]:
                    selected_product_ids.add(random.choice(pattern["addons"]))
                    
                if random.random() < 0.35:
                    selected_product_ids.add(random.randint(1, 95))
            else:
                # 28% belanja acak/campuran
                k = random.randint(2, 4)
                random_ids = random.sample(range(1, 96), k)
                selected_product_ids.update(random_ids)
                
            # Pastikan minimal 2 produk per transaksi untuk analisis Apriori
            while len(selected_product_ids) < 2:
                selected_product_ids.add(random.randint(1, 95))
                
            if len(selected_product_ids) > 6:
                selected_product_ids = set(random.sample(list(selected_product_ids), 6))
                
            for pid in sorted(selected_product_ids):
                prod = BY_ID[pid]
                product_usage_count[pid] += 1
                
                # Qty pembelian bervariasi tergantung tipe barang
                if "Beras" in prod["nama"] or "Minyak Goreng 2L" in prod["nama"]:
                    qty = random.choices([1, 2], weights=[0.85, 0.15])[0]
                elif "Mie" in prod["nama"] or "Indomie" in prod["nama"]:
                    qty = random.choices([1, 2, 3, 4, 5], weights=[0.25, 0.35, 0.20, 0.10, 0.10])[0]
                elif "Telur" in prod["nama"]:
                    qty = random.choices([1, 2], weights=[0.80, 0.20])[0]
                elif "sachet" in prod["nama"] or "renteng" in prod["nama"]:
                    qty = random.choices([1, 2, 3], weights=[0.60, 0.30, 0.10])[0]
                else:
                    qty = random.choices([1, 2], weights=[0.85, 0.15])[0]
                    
                total_harga = prod["harga"] * qty
                
                rows.append({
                    "no_transaksi": trx_code,
                    "tanggal_transaksi": date_str,
                    "id_produk": prod["id"],
                    "nama_produk": prod["nama"],
                    "kategori_produk": prod["kategori"],
                    "harga_satuan": prod["harga"],
                    "jumlah": qty,
                    "total_harga": total_harga
                })

        current_date += timedelta(days=1)
        
    # Pastikan setiap dari 95 produk ada transaksinya
    zero_usage = [pid for pid, cnt in product_usage_count.items() if cnt == 0]
    if zero_usage:
        for pid in zero_usage:
            prod = BY_ID[pid]
            sample_trx = rows[random.randint(0, len(rows)-1)]["no_transaksi"]
            sample_date = sample_trx.split("-")[1]
            date_formatted = f"{sample_date[:4]}-{sample_date[4:6]}-{sample_date[6:]}"
            rows.append({
                "no_transaksi": sample_trx,
                "tanggal_transaksi": date_formatted,
                "id_produk": prod["id"],
                "nama_produk": prod["nama"],
                "kategori_produk": prod["kategori"],
                "harga_satuan": prod["harga"],
                "jumlah": 1,
                "total_harga": prod["harga"]
            })
            product_usage_count[pid] += 1

    # Tulis HANYA dalam format COMMA (`,`)
    fieldnames = [
        "no_transaksi",
        "tanggal_transaksi",
        "id_produk",
        "nama_produk",
        "kategori_produk",
        "harga_satuan",
        "jumlah",
        "total_harga"
    ]
    
    with open(output_csv, mode="w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames, delimiter=",")
        writer.writeheader()
        writer.writerows(rows)

    total_trx = trx_counter - 1
    total_items = len(rows)
    print("SUKSES GENERATE DATASET TRANSAKSI SEMBAKO (3 BULAN TERAKHIR 2027)!")
    print(f"- Format Delimiter: Comma (,)")
    print(f"- Lokasi File: {output_csv}")
    print(f"- Rentang Waktu: {start_date} s.d. {end_date} (September - November 2027)")
    print(f"- Total Transaksi (Keranjang Belanja): {total_trx:,}")
    print(f"- Total Baris Item Transaksi: {total_items:,}")
    print(f"- Macam Produk: {len(PRODUCTS)} produk sembako")
    print(f"- Produk dengan kemunculan paling sedikit: {min(product_usage_count.values())}x")
    print(f"- Produk dengan kemunculan paling banyak: {max(product_usage_count.values())}x")

if __name__ == "__main__":
    generate_transactions("c:/laragon/www/tataletak/dataset_transaksi_sembako_2027.csv")
