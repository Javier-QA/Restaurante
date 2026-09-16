from pathlib import Path
import pandas as pd
import joblib

from sklearn.ensemble import RandomForestRegressor

BASE_DIR = Path(__file__).resolve().parent.parent
DATASET = BASE_DIR / "data" / "ventas_historicas.csv"
MODEL_DIR = BASE_DIR / "models"
MODEL_FILE = MODEL_DIR / "modelo_ventas.joblib"

MIN_DIAS_RECOMENDADOS = 30

print("=" * 55)
print("ENTRENAMIENTO - PREDICCION DE VENTAS")
print("=" * 55)

# ---------------------------------------------------------
# 1. Cargar datos
# ---------------------------------------------------------

df = pd.read_csv(DATASET)

df["fecha"] = pd.to_datetime(df["fecha"])
df["total"] = pd.to_numeric(df["total"], errors="coerce").fillna(0)

# ---------------------------------------------------------
# 2. Agrupar las ventas por dia
# ---------------------------------------------------------

ventas_diarias = (
    df.groupby("fecha", as_index=False)
      .agg(
          venta_total=("total", "sum"),
          unidades=("cantidad", "sum"),
          pedidos=("pedido_id", "nunique")
      )
      .sort_values("fecha")
)

# ---------------------------------------------------------
# 3. Crear caracteristicas para Machine Learning
# ---------------------------------------------------------

ventas_diarias["dia_semana"] = ventas_diarias["fecha"].dt.dayofweek
ventas_diarias["dia_mes"] = ventas_diarias["fecha"].dt.day
ventas_diarias["mes"] = ventas_diarias["fecha"].dt.month

X = ventas_diarias[
    [
        "dia_semana",
        "dia_mes",
        "mes"
    ]
]

y = ventas_diarias["venta_total"]

# ---------------------------------------------------------
# 4. Entrenar modelo
# ---------------------------------------------------------

modelo = RandomForestRegressor(
    n_estimators=200,
    random_state=42
)

modelo.fit(X, y)

# ---------------------------------------------------------
# 5. Guardar modelo y metadatos
# ---------------------------------------------------------

MODEL_DIR.mkdir(parents=True, exist_ok=True)

artefacto = {
    "modelo": modelo,
    "columnas": list(X.columns),
    "dias_entrenamiento": len(ventas_diarias),
    "fecha_inicio": str(ventas_diarias["fecha"].min().date()),
    "fecha_fin": str(ventas_diarias["fecha"].max().date()),
    "venta_total": float(ventas_diarias["venta_total"].sum()),
    "experimental": len(ventas_diarias) < MIN_DIAS_RECOMENDADOS,
}

joblib.dump(artefacto, MODEL_FILE)

# ---------------------------------------------------------
# 6. Resultado
# ---------------------------------------------------------

print()
print(f"Registros originales: {len(df)}")
print(f"Dias utilizados: {len(ventas_diarias)}")
print(f"Venta historica: S/ {ventas_diarias['venta_total'].sum():.2f}")
print(f"Periodo: {artefacto['fecha_inicio']} a {artefacto['fecha_fin']}")
print()

if artefacto["experimental"]:
    print("ESTADO: MODELO EXPERIMENTAL")
    print(
        f"Solo existen {len(ventas_diarias)} dias con ventas. "
        f"Se recomiendan al menos {MIN_DIAS_RECOMENDADOS} dias."
    )
else:
    print("ESTADO: DATOS SUFICIENTES PARA EVALUACION")

print()
print(f"Modelo guardado en: {MODEL_FILE}")
print("=" * 55)