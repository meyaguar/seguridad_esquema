# 📌 Proyecto: Esquema de Seguridad en Bases de Datos

## 📂 **Estructura del Proyecto**

```
/seguridad_esquema/
│── /config/
│   ├── database.php         # Configuración de conexión a la base de datos (usando PDO)
│── /backup/
│   ├── backup.php           # Script para generar copias de seguridad cifradas de la BD
│   ├── restore.php          # Script para restaurar copias de seguridad cifradas de la BD
│── /encryption/
│   ├── aes_encryption.php   # Implementación de cifrado y descifrado AES-256-CBC
│   ├── sha256_hash.php      # Generación y verificación de hashes con SHA-256
│── /db_scripts/
│   ├── schema.sql           # Estructura de la base de datos (subida por el usuario)
│   ├── seed.sql             # Datos de prueba iniciales para poblar la base de datos
│── /tests/
│   ├── test.backup.php      # Prueba automatizada para la generación de respaldos cifrados
│   ├── test.restore.php     # Prueba automatizada para la restauración de respaldos cifrados
│── .env                     # Archivo de configuración de variables de entorno
│── README.md                # Documentación del proyecto
```

## 🚀 **Descripción del Proyecto**

Este proyecto implementa un **esquema de seguridad** que combina **copias de seguridad cifradas** y **cifrado de datos**, garantizando la disponibilidad y confidencialidad de la información en una base de datos MySQL.

- **Las copias de seguridad son cifradas con AES-256-CBC** antes de ser almacenadas.
- **El proceso de restauración descifra automáticamente el respaldo** antes de cargarlo en la base de datos.
- **Se implementa verificación de integridad** mediante SHA-256.

## ⚙️ **Requisitos**

- PHP 7.4+
- MySQL 5.7+
- OpenSSL habilitado en PHP
- Acceso a línea de comandos (CLI)

## 🔧 **Configuración**

1. **Clona el repositorio:**

   ```bash
   git clone https://github.com/usuario/seguridad_esquema.git
   cd seguridad_esquema
   ```

2. **Crea el archivo **`.env`** en la raíz del proyecto** con los siguientes valores:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=[localhost o IP]
   DB_PORT=3306
   DB_DATABASE=db_main
   DB_USERNAME=admpos
   DB_PASSWORD=Pos2025!
   DB_CHARSET=utf8mb4
   BACKUP_ENCRYPTION_KEY=ClaveSeguraParaBackup2025
   ```

3. **Ejecuta los scripts SQL para inicializar la base de datos:**

   ```bash
   mysql -u admpos -p db_main < db_scripts/schema.sql
   mysql -u admpos -p db_main < db_scripts/seed.sql
   ```

## 🛠 **Uso del Backup y Restauración**

### 🔹 **Generar un Backup Cifrado**

```bash
php backup/backup.php
```

✅ Esto creará un archivo cifrado en la carpeta `/backup/` con extensión `.enc`.

### 🔹 **Restaurar un Backup Cifrado**

```bash
php backup/restore.php /ruta/al/backup.enc
```

✅ Esto descifrará y restaurará el backup en la base de datos.

## 🧪 **Pruebas Automáticas**

### 🔹 **Probar el Backup**

```bash
php tests/test.backup.php
```

✅ Ejecuta una prueba automatizada para verificar que el respaldo se genera correctamente.

### 🔹 **Probar la Restauración**

```bash
php tests/test.restore.php
```

✅ Restaura automáticamente el último backup cifrado disponible.

## 🛡 **Seguridad Implementada**

- **AES-256-CBC** para cifrar las copias de seguridad.
- **SHA-256** para verificar la integridad de los datos.
- **Eliminación automática del backup no cifrado** después del proceso de cifrado.

---

🚀 **Desarrollado por:** YAGUAR, Eduardo

