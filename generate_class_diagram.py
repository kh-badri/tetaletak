import os
from PIL import Image, ImageDraw, ImageFont

def generate_black_white_uml_diagram():
    # Dimensi kanvas resolusi tinggi (1920 x 1220 px)
    width = 1920
    height = 1220
    img = Image.new("RGB", (width, height), color="#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Load Font Windows Segoe UI
    font_main_title = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 26)
    font_main_sub = ImageFont.truetype("C:/Windows/Fonts/segoeui.ttf", 15)
    font_class_title = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 21)
    font_bold = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 14)
    font_regular = ImageFont.truetype("C:/Windows/Fonts/segoeui.ttf", 14)
    font_label = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 13)
    font_legend_title = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 15)
    font_legend_header = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 13)
    font_legend_text = ImageFont.truetype("C:/Windows/Fonts/segoeui.ttf", 13)

    # 1. Judul Utama di Atas (Hitam Murni)
    draw.text((width // 2, 42), "CLASS DIAGRAM DATABASE - SISTEM REKOMENDASI TATA LETAK PRODUK", 
              fill="#000000", font=font_main_title, anchor="mm")
    draw.text((width // 2, 72), "Struktur Tabel, Relasi Relasional (Foreign Key), dan Operasi Model pada Database tataletak_db (Algoritma Apriori)", 
              fill="#000000", font=font_main_sub, anchor="mm")

    # 2. Definisi Struktur Kelas / Tabel Database (Hitam Putih Murni)
    classes = {
        "login": {
            "x": 80,
            "y": 140,
            "w": 390,
            "title": "login",
            "attributes": [
                "- id : int (11)",
                "- username : varchar (50)",
                "- password : varchar (255)",
                "- nama_lengkap : varchar (255)",
                "- email : varchar (255)",
                "- foto : varchar (255)"
            ],
            "methods": [
                "+ login()",
                "+ logout()",
                "+ updateProfil()",
                "+ updateSandi()"
            ]
        },
        "produk": {
            "x": 650,
            "y": 140,
            "w": 440,
            "title": "produk",
            "attributes": [
                "- id : int (11)",
                "- nama_produk : varchar (150)",
                "- kategori_produk : varchar (100)",
                "- harga_satuan : int (11)",
                "- satuan : varchar (50)",
                "- created_at : datetime",
                "- updated_at : datetime"
            ],
            "methods": [
                "+ simpan()",
                "+ edit()",
                "+ hapus()"
            ]
        },
        "dataset_produk": {
            "x": 650,
            "y": 550,
            "w": 440,
            "title": "dataset_produk",
            "attributes": [
                "- id : int (11)",
                "- no_transaksi : varchar (50)",
                "- waktu_penjualan : date",
                "- id_produk : int (11)",
                "- nama_produk : varchar (150)",
                "- kategori_produk : varchar (100)",
                "- harga_produk_rp : int (11)",
                "- jumlah_terjual_unit : int (11)",
                "- total_harga : int (11)",
                "- status_promosi : varchar (10)",
                "- terjadi_penurunan : varchar (10)",
                "- created_at : datetime",
                "- updated_at : datetime"
            ],
            "methods": [
                "+ simpan()",
                "+ edit()",
                "+ hapus()",
                "+ uploadCsv()",
                "+ exportCsv()"
            ]
        },
        "history_analisis": {
            "x": 1270,
            "y": 480,
            "w": 490,
            "title": "history_analisis",
            "attributes": [
                "- id : int (11)",
                "- tanggal_analisis : datetime",
                "- accuracy : decimal (5,2)",
                "- total_data : int (11)",
                "- hasil_analisis : longtext",
                "- hasil_evaluasi : longtext",
                "- hasil_prediksi : longtext",
                "- created_at : timestamp"
            ],
            "methods": [
                "+ simpan()",
                "+ detail()",
                "+ hapus()"
            ]
        }
    }

    # Hitung posisi dan ukuran masing-masing box
    line_h = 24
    box_coords = {}

    for cname, cdata in classes.items():
        x = cdata["x"]
        y = cdata["y"]
        w = cdata["w"]
        
        header_h = 48
        attr_h = 16 + len(cdata["attributes"]) * line_h
        meth_h = 16 + len(cdata["methods"]) * line_h
        total_h = header_h + attr_h + meth_h

        box_coords[cname] = {
            "x": x, "y": y, "w": w, "h": total_h,
            "header_h": header_h, "attr_h": attr_h, "meth_h": meth_h,
            "r": x + w, "b": y + total_h,
            "cx": x + w // 2, "cy": y + total_h // 2
        }

    # 3. Fungsi Gambar Panah Orthogonal Hitam Putih
    def draw_arrow(points, label="", label_pos=None, label_anchor="mm"):
        line_color = "#000000"
        line_width = 2
        
        # Garis penghubung
        for i in range(len(points) - 1):
            draw.line([points[i], points[i+1]], fill=line_color, width=line_width)
            
        # Kepala panah di ujung garis
        p_from = points[-2]
        p_to = points[-1]
        
        dx = p_to[0] - p_from[0]
        dy = p_to[1] - p_from[1]
        
        sz = 8
        if dx > 0: # panah ke kanan
            tip = p_to
            left = (p_to[0] - sz, p_to[1] - sz)
            right = (p_to[0] - sz, p_to[1] + sz)
        elif dx < 0: # panah ke kiri
            tip = p_to
            left = (p_to[0] + sz, p_to[1] - sz)
            right = (p_to[0] + sz, p_to[1] + sz)
        elif dy > 0: # panah ke bawah
            tip = p_to
            left = (p_to[0] - sz, p_to[1] - sz)
            right = (p_to[0] + sz, p_to[1] - sz)
        else: # panah ke atas
            tip = p_to
            left = (p_to[0] - sz, p_to[1] + sz)
            right = (p_to[0] + sz, p_to[1] + sz)
            
        draw.polygon([tip, left, right], fill=line_color)

        # Label Relasi (Hitam di atas latar putih bersih dengan border tipis hitam)
        if label and label_pos:
            bbox = draw.textbbox(label_pos, label, font=font_label, anchor=label_anchor)
            draw.rectangle([bbox[0]-6, bbox[1]-3, bbox[2]+6, bbox[3]+3], fill="#FFFFFF", outline="#000000", width=1)
            draw.text(label_pos, label, fill="#000000", font=font_label, anchor=label_anchor)

    # -------------------------------------------------------------
    # 4. GAMBAR PANAH RELASI ANTAR TABEL
    # -------------------------------------------------------------

    # Relasi 1: login -> produk
    p1_start = (box_coords["login"]["r"], box_coords["login"]["y"] + 70)
    p1_end   = (box_coords["produk"]["x"], box_coords["produk"]["y"] + 70)
    draw_arrow([p1_start, p1_end], label="1..* mengelola", label_pos=((p1_start[0] + p1_end[0]) // 2, p1_start[1]))

    # Relasi 2: login -> dataset_produk
    p2_start = (box_coords["login"]["r"], box_coords["login"]["y"] + 220)
    p2_c1    = (560, box_coords["login"]["y"] + 220)
    p2_c2    = (560, box_coords["dataset_produk"]["y"] + 110)
    p2_end   = (box_coords["dataset_produk"]["x"], box_coords["dataset_produk"]["y"] + 110)
    draw_arrow([p2_start, p2_c1, p2_c2, p2_end], 
               label="1..* input / import CSV", 
               label_pos=(560, (p2_c1[1] + p2_c2[1]) // 2))

    # Relasi 3: produk -> dataset_produk (FK id_produk)
    p3_start = (box_coords["produk"]["cx"], box_coords["produk"]["b"])
    p3_end   = (box_coords["dataset_produk"]["cx"], box_coords["dataset_produk"]["y"])
    draw_arrow([p3_start, p3_end], 
               label="1..* (id_produk)", 
               label_pos=(p3_start[0], (p3_start[1] + p3_end[1]) // 2))

    # Relasi 4: dataset_produk -> history_analisis
    p4_start = (box_coords["dataset_produk"]["r"], box_coords["dataset_produk"]["y"] + 160)
    p4_c1    = (1180, box_coords["dataset_produk"]["y"] + 160)
    p4_c2    = (1180, box_coords["history_analisis"]["y"] + 160)
    p4_end   = (box_coords["history_analisis"]["x"], box_coords["history_analisis"]["y"] + 160)
    draw_arrow([p4_start, p4_c1, p4_c2, p4_end], 
               label="diproses Apriori", 
               label_pos=(1180, (p4_c1[1] + p4_c2[1]) // 2))

    # Relasi 5: login -> history_analisis
    p5_start = (box_coords["login"]["cx"], box_coords["login"]["b"])
    p5_c1    = (box_coords["login"]["cx"], 1150)
    p5_c2    = (box_coords["history_analisis"]["cx"], 1150)
    p5_end   = (box_coords["history_analisis"]["cx"], box_coords["history_analisis"]["b"])
    draw_arrow([p5_start, p5_c1, p5_c2, p5_end], 
               label="1..* menyimpan & melihat riwayat", 
               label_pos=((p5_c1[0] + p5_c2[0]) // 2, 1150))


    # -------------------------------------------------------------
    # 5. GAMBAR KOTAK KELAS UML HITAM PUTIH (3 KOMPARTEMEN)
    # -------------------------------------------------------------
    border_color = "#000000"

    for cname, cdata in classes.items():
        coords = box_coords[cname]
        x, y, w, h = coords["x"], coords["y"], coords["w"], coords["h"]
        hh = coords["header_h"]
        ah = coords["attr_h"]

        # Outer Box Border (Hitam Putih Murni)
        draw.rectangle([x, y, x + w, y + h], fill="#FFFFFF", outline=border_color, width=2)

        # Header Box Separator Line
        draw.line([(x, y + hh), (x + w, y + hh)], fill=border_color, width=2)

        # Header Text (Nama Tabel - Bold Hitam di Tengah)
        draw.text((x + w // 2, y + hh // 2), cdata["title"], fill="#000000", font=font_class_title, anchor="mm")

        # Separator Line antara Atribut & Metode
        sep_y = y + hh + ah
        draw.line([(x, sep_y), (x + w, sep_y)], fill=border_color, width=2)

        # Atribut List (Kompartemen 2 - Hitam Murni)
        curr_y = y + hh + 12
        for attr_text in cdata["attributes"]:
            draw.text((x + 16, curr_y), attr_text, fill="#000000", font=font_regular)
            curr_y += line_h

        # Metode List (Kompartemen 3 - Hitam Murni)
        curr_y = sep_y + 12
        for meth_text in cdata["methods"]:
            draw.text((x + 16, curr_y), meth_text, fill="#000000", font=font_regular)
            curr_y += line_h


    # -------------------------------------------------------------
    # 6. KOTAK LEGENDA / PENJELASAN RELASI (Hitam Putih Murni)
    # -------------------------------------------------------------
    leg_x = 1270
    leg_y = 140
    leg_w = 490
    leg_h = 285

    draw.rectangle([leg_x, leg_y, leg_x + leg_w, leg_y + leg_h], fill="#FFFFFF", outline=border_color, width=2)
    draw.line([(leg_x, leg_y + 40), (leg_x + leg_w, leg_y + 40)], fill=border_color, width=2)

    draw.text((leg_x + 16, leg_y + 20), "PENJELASAN RELASI DATABASE:", fill="#000000", font=font_legend_title, anchor="lm")

    explanations = [
        ("1. login -> produk (1 to N):", "Satu admin mengelola data master 95 produk sembako."),
        ("2. login -> dataset_produk (1 to N):", "Admin menginput manual atau import file CSV transaksi belanja."),
        ("3. produk -> dataset_produk (1 to N):", "Foreign key id_produk menghubungkan detail transaksi ke master."),
        ("4. dataset_produk -> history_analisis (N to 1):", "Kumpulan transaksi belanja dihitung oleh Algoritma Apriori"),
        ("   (Support, Confidence, Lift) menjadi rekomendasi rak.", ""),
        ("5. login -> history_analisis (1 to N):", "Admin menjalankan proses analisis dan melihat riwayat laporan.")
    ]

    cur_ly = leg_y + 54
    for header, desc in explanations:
        if header.strip():
            draw.text((leg_x + 16, cur_ly), header, fill="#000000", font=font_legend_header)
            cur_ly += 18
        if desc.strip():
            draw.text((leg_x + 24, cur_ly), desc, fill="#000000", font=font_legend_text)
            cur_ly += 22

    # 7. Simpan File dalam Semua Format (PNG, JPG, WebP)
    out_dir = "c:/laragon/www/tataletak"
    pub_dir = "c:/laragon/www/tataletak/public/images"
    art_dir = "C:/Users/khbad/.gemini/antigravity/brain/ad0295fe-29e7-418c-b91e-b96d8b1415a0"

    formats = [
        (os.path.join(out_dir, "class_diagram_database.png"), "PNG"),
        (os.path.join(out_dir, "class_diagram_database.jpg"), "JPEG"),
        (os.path.join(out_dir, "class_diagram_database.webp"), "WEBP"),
        (os.path.join(pub_dir, "class_diagram_database.png"), "PNG"),
        (os.path.join(pub_dir, "class_diagram_database.jpg"), "JPEG"),
        (os.path.join(pub_dir, "class_diagram_database.webp"), "WEBP"),
        (os.path.join(art_dir, "class_diagram_database.png"), "PNG"),
        (os.path.join(art_dir, "class_diagram_database.jpg"), "JPEG"),
        (os.path.join(art_dir, "class_diagram_database.webp"), "WEBP")
    ]

    for fpath, fmt in formats:
        if fmt == "JPEG":
            img.convert("RGB").save(fpath, fmt, quality=98, dpi=(300, 300))
        elif fmt == "WEBP":
            img.save(fpath, fmt, quality=98)
        else:
            img.save(fpath, fmt, dpi=(300, 300))

    print("Diagram Hitam Putih (Black & White) berhasil di-generate secara sempurna!")

if __name__ == "__main__":
    generate_black_white_uml_diagram()
