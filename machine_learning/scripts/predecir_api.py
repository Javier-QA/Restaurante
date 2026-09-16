import sys
import json
from pathlib import Path
from datetime import date, timedelta

import joblib
import pandas as pd

BASE_DIR = Path(__file__).resolve().parent.parent
MODEL_FILE = BASE_DIR / "models" / "modelo_ventas.joblib"


def main():
    if not MODEL_FILE.exists():
        print(json.dumps({
            "success": False,
            "error": "Modelo de ventas no encontrado."
        }, ensure_ascii=False))
        return

    try:
        artefacto = joblib.load(MODEL_FILE)
        modelo = artefacto["modelo"]
        columnas = artefacto["columnas"]

        if len(sys.argv) > 1:
            fecha = date.fromisoformat(sys.argv[1])
        else:
            fecha = date.fromisoformat(
                artefacto["fecha_fin"]
            ) + timedelta(days=1)

        datos = pd.DataFrame([{
            "dia_semana": fecha.weekday(),
            "dia_mes": fecha.day,
            "mes": fecha.month
        }])

        datos = datos[columnas]

        prediccion = float(modelo.predict(datos)[0])

        resultado = {
            "success": True,
            "fecha": fecha.isoformat(),
            "venta_estimada": round(max(0, prediccion), 2),
            "dias_entrenamiento": artefacto["dias_entrenamiento"],
            "experimental": artefacto["experimental"],
            "fecha_inicio_entrenamiento": artefacto["fecha_inicio"],
            "fecha_fin_entrenamiento": artefacto["fecha_fin"]
        }

        print(json.dumps(resultado, ensure_ascii=False))

    except Exception as e:
        print(json.dumps({
            "success": False,
            "error": str(e)
        }, ensure_ascii=False))


if __name__ == "__main__":
    main()