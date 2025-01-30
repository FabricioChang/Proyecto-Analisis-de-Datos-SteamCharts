# Importar librerías
import os
import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt

# Configurar estilo de gráficos
sns.set(style="whitegrid")

# Rutas
diccionario_rutas = {
    "World of Warcraft" : "datos/World of Warcraft.csv",
    "The Lord of the Rings Online" : "datos/The Lord of the Rings Online™.csv",
    "The Elder Scrolls Online" : "datos/The Elder Scrolls Online.csv",
    "Star Wars: The Old Republic" : "datos/STAR WARS™: The Old Republic™.csv",
    "RuneScape" : "datos/RuneScape.csv",
    "Project Gorgon" : "datos/Project: Gorgon.csv",
    "Path of Exile 2" : "datos/Path of Exile 2.csv",
    "Path of Exile" : "datos/Path of Exile.csv",
    "Old School RuneScape" : "datos/Old School RuneScape.csv",
    "Lost Ark" : "datos/Lost Ark.csv",
    "Guild Wars 2" : "datos/Guild Wars 2.csv",
    "Final Fantasy" : "datos/FINAL FANTASY XIV Online.csv",
    "Adventure Quest" : "datos/AdventureQuest 3D.csv",
    "Throne and Liberty" : "datos/THRONE AND LIBERTY.csv"
}

# Carga de datos
diccionario_datos = {}
for x,y in diccionario_rutas.items():
    try:
        if x != "Path of Exile 2":
            diccionario_datos[x] = pd.read_csv(y)
            print(f'Datos de {x} cargados con éxito!\n')
    except Exception as e:
        print(f'Error al cargar los datos de {x}\n')
lista_juegos = list(diccionario_datos.keys())
print(lista_juegos)

diccionario_cantidad = {}
for x,y in diccionario_datos.items():
    diccionario_cantidad[x] = len(y)
    
print(diccionario_cantidad)

print(diccionario_datos["World of Warcraft"].head())

os.makedirs("graficos", exist_ok=True)

for x,y in diccionario_datos.items():
    y = y[y['fecha'] != 'Last 30 Days']
    y = y.iloc[::-1].reset_index(drop=True)
    plt.figure(figsize=(14, 6))
    
    # Escalar promedios para "World of Warcraft" (en millones)
    if x == "World of Warcraft":
        y['promedio'] = y['promedio'].str.replace(',', '').astype(int) / 1_000_000
        ylabel = "Promedio (en millones)"
    else:
        y = y.iloc[:-1]
        ylabel = "Promedio"
        
    # Crear el gráfico de barras
    plt.bar(y['fecha'], y['promedio'], color='skyblue', edgecolor='black')
    plt.title(f'Promedios por Fecha {x}', fontsize=16)
    plt.xlabel('Fecha', fontsize=14)
    plt.ylabel(ylabel, fontsize=14)

    # Reducir los rotulos del eje x para datasets grandes
    if len(y) > 20:
        plt.xticks(range(0, len(y), max(1, len(y) // 10)), y['fecha'][::max(1, len(y) // 10)], rotation=45, ha='right', fontsize=10)
    else:
        plt.xticks(rotation=45, ha='right', fontsize=12)

    plt.grid(axis='y', linestyle='--', alpha=0.7)
    plt.tight_layout()
    #plt.show()
    
    ruta_grafico = os.path.join("graficos", f"{x}.png")
    plt.savefig(ruta_grafico)
    plt.close()

    print(f"Gráfico guardado: {ruta_grafico}")

