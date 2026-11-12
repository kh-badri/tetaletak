# writable/python/run_analysis_nb.py

import pandas as pd
import mysql.connector
import json
import sys
import warnings

warnings.filterwarnings("ignore", category=UserWarning)

from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OrdinalEncoder, MinMaxScaler, LabelEncoder
from sklearn.naive_bayes import GaussianNB
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
from sklearn.compose import ColumnTransformer

def run_analysis():
    try:
        # --- 1. KONEKSI DATABASE ---
        if len(sys.argv) != 5:
            raise Exception("Skrip Python mengharapkan 4 argumen: host, user, password, database.")

        db_host, db_user, db_pass, db_name = sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4]

        db = mysql.connector.connect(
            host=db_host, user=db_user, password=db_pass, database=db_name
        )

        # --- 2. AMBIL DATA ---
        df = pd.read_sql("SELECT * FROM dataset_produk", db)
        db.close()

        if df.empty:
            raise Exception("Tabel 'dataset_produk' kosong.")

        # --- 3. PREPROCESSING ---
        df['waktu_penjualan'] = pd.to_datetime(df['waktu_penjualan'])
        df['bulan'] = df['waktu_penjualan'].dt.month
        df['tahun'] = df['waktu_penjualan'].dt.year

        target_column = 'terjadi_penurunan'
        
        # Simpan fitur yang dibutuhkan untuk tampilan di PHP nanti
        X = df.drop(columns=[target_column, 'id', 'created_at', 'updated_at', 'waktu_penjualan'])
        y_raw = df[target_column]

        le = LabelEncoder()
        y = le.fit_transform(y_raw)
        
        categorical_features = ['nama_produk', 'kategori_produk', 'status_promosi']
        numerical_features = ['harga_produk_rp', 'jumlah_terjual_unit']
        
        preprocessor = ColumnTransformer(
            transformers=[
                ('cat', OrdinalEncoder(handle_unknown='use_encoded_value', unknown_value=-1), categorical_features),
                ('num', MinMaxScaler(), numerical_features)
            ],
            remainder='passthrough'
        )

        # --- 4. TRAIN-TEST SPLIT ---
        # Kita gunakan random_state=42 agar data uji yang muncul di PHP konsisten
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

        # --- 5. TRANSFORMASI ---
        X_train_processed = preprocessor.fit_transform(X_train)
        X_test_processed = preprocessor.transform(X_test)
        
        # --- 6. TRAINING ---
        model = GaussianNB()
        model.fit(X_train_processed, y_train)
        
        # --- 7A. EVALUASI METRIK ---
        y_pred = model.predict(X_test_processed)
        y_pred_labels = le.inverse_transform(y_pred)
        y_test_labels = le.inverse_transform(y_test)
        
        accuracy = accuracy_score(y_test, y_pred)
        cm = confusion_matrix(y_test_labels, y_pred_labels, labels=['Tidak', 'Ya'])
        
        if cm.shape == (1, 1):
            tn, fp, fn, tp = (cm[0][0], 0, 0, 0) if y_test_labels[0] == 'Tidak' else (0, 0, 0, cm[0][0])
        else:
            tn, fp, fn, tp = cm.ravel()

        report = classification_report(y_test_labels, y_pred_labels, target_names=['Tidak', 'Ya'], output_dict=True, zero_division=0)
        
        # --- 7B. ANALISIS RATA-RATA (Untuk Interpretasi) ---
        feature_names_processed = preprocessor.get_feature_names_out()
        analysis_results = []
        for i, feature_name in enumerate(feature_names_processed):
            if 'num__' in feature_name:
                display_name = feature_name.replace('num__', '')
                scaler = preprocessor.named_transformers_['num']
                idx = numerical_features.index(display_name)
                
                # Inverse transform sederhana
                val_t = (model.theta_[0][i] * (scaler.data_max_[idx] - scaler.data_min_[idx])) + scaler.data_min_[idx]
                val_y = (model.theta_[1][i] * (scaler.data_max_[idx] - scaler.data_min_[idx])) + scaler.data_min_[idx]
            elif 'remainder__' in feature_name:
                display_name = feature_name.replace('remainder__', '')
                val_t, val_y = model.theta_[0][i], model.theta_[1][i]
            else:
                continue

            analysis_results.append({
                "fitur": display_name,
                "rata_rata_tidak_turun": float(val_t),
                "rata_rata_ya_turun": float(val_y)
            })

        # --- 7C. DETAIL PREDIKSI PER PRODUK (PERBAIKAN UTAMA) ---
        # Menggabungkan data input X_test dengan hasil prediksi y_pred_labels
        test_results_df = X_test.copy()
        test_results_df['prediksi'] = y_pred_labels
        # Ubah ke list of dictionary agar bisa dibaca JSON PHP
        detail_prediksi = test_results_df.to_dict(orient='records')

        # --- 8. OUTPUT JSON ---
        results = {
            "status": "success",
            "evaluasi": {
                "total_data": len(df),
                "total_training": len(X_train),
                "total_testing": len(X_test),
                "accuracy": accuracy * 100,
                "confusion_matrix": {
                    "true_negative": int(tn), "false_positive": int(fp),
                    "false_negative": int(fn), "true_positive": int(tp)
                },
                "classification_report": report
            },
            "analisis": analysis_results,
            "detail_prediksi": detail_prediksi  # <-- DATA INI YANG DITUNGGU PHP
        }
        
        print(json.dumps(results, indent=4))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    run_analysis()