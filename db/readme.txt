https://www.sqlite.org/cli.html

1. Crear una Base de Datos sqlite
$ sqlite3 dbname./db/sqlite/sqlite3/db3

2. Leer SQL desde un archivo (por ejm. el DDL en el archivo schema.sql)
sqlite> .read c:/route/to/schema.sql

3. Importación de archivos CSV
Movemos el archivo descargado MOCK_DATA.csv al directorio de trabajo y a su vez lo renombramos como user.csv
$ mv /c/Users/neo/Downloads/MOCK_DATA.csv user.csv
sqlite> .show (para verificar si el modo csv esta activado)
sqlite> .mode csv (activamos el mode csv para importar los datos)
sqlite> .import C:/xampp/htdocs/test/db/user.csv user
NOTA: cuando importamos archivos csv a sqlite, debemos asegurarnos de que esten todos los campos, al igual que en el archivo de schema.sql

https://www.mockaroo.com/
Datos aleatorios para testear la app