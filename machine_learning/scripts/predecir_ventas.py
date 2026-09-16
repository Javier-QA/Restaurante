from pathlib import Path
from datetime import date, timedelta
import joblib
import pandas as pd

BASE_DIR = Path(__file__).resolve().parent.parent
MODEL_FILE = BASE_DIR / "models" / "modelo_ventas.joblib"

# Cargar modelo
artefacto = joblib.load(MODEL_FILE)

modelo = artefacto["modelo"]
columnas = artefacto["columnas"]

# Obtener el dia siguiente al ultimo dia del entrenamiento
ultima_fecha = date.fromisoformat(artefacto["fecha_fin"])
fecha_prediccion = ultima_fecha + timedelta(days=1)

# Crear las mismas caracteristicas utilizadas al entrenar
datos = pd.DataFrame([{
    "dia_semana": fecha_prediccion.weekday(),
    "dia_mes": fecha_prediccion.day,
    "mes": fecha_prediccion.month,
}])

datos = datos[columnas]

# Realizar prediccion
prediccion = float(modelo.predict(datos)[0])

print("=" * 55)
print("PREDICCION DE VENTAS")
print("=" * 55)

print(f"Fecha a predecir: {fecha_prediccion}")
print(f"Venta estimada: S/ {prediccion:.2f}")
print(f"Dias usados para entrenar: {artefacto['dias_entrenamiento']}")

if artefacto["experimental"]:
    print("Estado: EXPERIMENTAL")
    print("La prediccion aun no debe considerarse confiable.")
else:
    print("Estado: MODELO CON HISTORIAL SUFICIENTE")

print("=" * 55)