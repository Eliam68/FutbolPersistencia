# FutbolPersistencia

Una aplicación web PHP para gestionar equipos y partidos de fútbol, con persistencia en base de datos MySQL. Enfocada en la capa de datos (DAO pattern) y validaciones del lado del servidor, sin dependencias de JavaScript para la lógica principal.

## Descripción

El proyecto permite:
- Gestionar equipos (añadir, listar).
- Gestionar partidos por jornadas (añadir, listar resultados).
- Ver partidos de un equipo específico.
- Auto-relleno del estadio basado en el equipo local (solo con PHP).
- Sesiones para recordar el último equipo consultado y redirigir a la página principal correspondiente.

**Enfoque principal:** Backend y persistencia de datos. La interfaz es funcional pero minimalista, priorizando la lógica del servidor.

## Tecnologías

- **PHP 7.4+** (procedural, sin frameworks).
- **MySQL/MariaDB** (via mysqli).
- **XAMPP** (recomendado para desarrollo local).
- **Bootstrap 5** (para estilos básicos en vistas).
- **Arquitectura:** MVC-like con DAOs (Data Access Objects), Singleton para conexión DB.

## Instalación y Configuración

### Prerrequisitos
- XAMPP (o similar) con Apache y MySQL activados.
- PHP con extensión mysqli habilitada.
- Git (opcional, para clonar el repo).

### Pasos de Instalación

1. **Clona o descarga el proyecto:**
   ```bash
   git clone https://github.com/Eliam68/FutbolPersistencia.git
   cd FutbolPersistencia
   ```

2. **Configura la base de datos:**
   - Abre XAMPP Control Panel y arranca MySQL.
   - Importa el esquema y datos desde `persistence/sql/futbol_persistence.sql`:
     - Opción A (phpMyAdmin): Ve a http://localhost/phpmyadmin → Crea una nueva BD llamada `futbol_persistencia` → Importa el archivo SQL.
     - Opción B (línea de comandos): Desde PowerShell (ajusta rutas si es necesario):
       ```powershell
       & 'C:\xampp\mysql\bin\mysql.exe' -u root < 'persistence\sql\futbol_persistence.sql'
       ```
     - Si tienes contraseña en MySQL, añade `-p` al comando.

3. **Configura credenciales de BD:**
   - Edita `persistence/conf/credentials.json` con tus datos de conexión (por defecto usa root sin contraseña):
     ```json
     {
         "host": "127.0.0.1",
         "user": "root",
         "password": "",
         "name": "futbol_persistencia"
     }
     ```

4. **Despliega en XAMPP:**
   - Copia la carpeta del proyecto a `C:\xampp\htdocs\DAM1\DesarrolloWeb\FutbolPersistencia` (o ajusta según tu estructura).
   - Accede a http://localhost/DAM1/DesarrolloWeb/FutbolPersistencia/index.php.

5. **Verifica:**
   - La página principal debería redirigir a `app/Equipos.php` (lista de equipos).
   - Añade equipos y partidos para probar.

## Uso

- **Página principal (`index.php`):** Redirige basado en sesión (último equipo visto) o a equipos si no hay sesión.
- **Equipos (`app/Equipos.php`):** Lista equipos con enlaces a sus partidos. Formulario para añadir equipo (nombre y estadio).
- **Partidos (`app/Partidos.php`):** Lista partidos por jornada. Formulario para añadir partido (auto-relleno estadio al cambiar equipo local).
- **Partidos de Equipo (`app/PartidosEquipo.php`):** Muestra partidos de un equipo (local/visitante), guarda en sesión.

**Flujo típico:**
1. Añade equipos.
2. Añade partidos (elige jornada, equipos, resultado opcional; estadio se rellena automáticamente).
3. Haz clic en un equipo para ver sus partidos (se guarda en sesión).

## Arquitectura y Estructura

```
FutbolPersistencia/
├── index.php                 # Punto de entrada, redirección basada en sesión
├── app/
│   ├── Equipos.php           # Vista/formulario de equipos
│   ├── Partidos.php          # Vista/formulario de partidos (con auto-relleno)
│   └── PartidosEquipo.php    # Vista de partidos de un equipo
├── persistence/
│   ├── conf/
│   │   ├── credentials.json  # Credenciales BD (NO subir a Git)
│   │   └── PersistentManager.php  # Singleton para conexión DB
│   ├── DAO/
│   │   ├── GenericDAO.php    # Base para DAOs
│   │   ├── EquiposDAO.php    # CRUD equipos
│   │   ├── PartidosDAO.php   # CRUD partidos + queries
│   │   └── UserDAO.php       # (No usado, placeholder)
│   └── sql/
│       └── futbol_persistence.sql  # Esquema BD + datos de ejemplo
├── templates/                # Vistas compartidas (header, footer, etc.)
├── utils/
│   └── SessionHelper.php     # Utilidades de sesión
├── assets/                   # Bootstrap CSS/JS
└── scripts/                  # Scripts auxiliares (ej. import DB)
```

### Base de Datos
- **Tablas:** equipos, jornadas, partidos.
- **Relaciones:** FK entre partidos y equipos/jornadas.
- **Constraints:** UNIQUE en partidos (evita duplicados por jornada), CHECK (equipos distintos).
- **Estadio:** NOT NULL, auto-relleno desde equipo local.

### DAOs
- Extienden `GenericDAO` (conexión singleton).
- Métodos: selectAll, selectById, insert, delete + queries específicas (ej. getByJornada).

### Sesiones
- Usadas para recordar equipo consultado (redirección en index.php).
- Helper en `utils/SessionHelper.php`.

## Mejoras Futuras y Opciones de Desarrollo

### Seguridad
- **CSRF Tokens:** Añadir tokens en formularios (`Equipos.php`, `Partidos.php`). Implementar en `SessionHelper.php` (generar token único por sesión, validar en POST).
- **Validaciones:** Sanitizar inputs (usar `filter_var`), escapar outputs (ya se hace con `htmlspecialchars`).
- **Autenticación:** Añadir login/logout si se expande a multi-usuario.

### Funcionalidad
- **Editar/Borrar:** Añadir opciones para modificar equipos/partidos (forms adicionales).
- **Paginación:** En listas largas (equipos/partidos).
- **Búsqueda/Filtros:** Buscar partidos por fecha, resultado, etc.
- **API REST:** Exponer endpoints JSON para equipos/partidos (usar `json_encode` en PHP).
- **Notificaciones:** Mensajes flash mejorados (usar sesiones para success/error).

### Rendimiento y Escalabilidad
- **Prepared Statements:** Ya implementados en DAOs (bueno para SQL injection).
- **Caché:** Añadir APCu o Redis para queries frecuentes.
- **ORM:** Migrar a Doctrine o Eloquent si crece el proyecto.
- **Testing:** Añadir PHPUnit para DAOs y vistas (tests unitarios/integration).

### UI/UX
- **JavaScript:** Añadir para validaciones client-side o AJAX (ej. auto-relleno sin recarga, pero mantener server-side).
- **Frameworks:** Migrar a Laravel/Symfony para estructura MVC completa.
- **Responsive:** Mejorar con Bootstrap grids o Tailwind.

### DevOps
- **Docker:** Contenedorizar (Dockerfile con PHP + MySQL).
- **CI/CD:** GitHub Actions para linting (PHPStan) y tests.
- **Deployment:** Scripts para producción (ej. en Heroku/VPS).

### Otros
- **Logging:** Añadir Monolog para errores/DB.
- **Internacionalización:** Soporte multi-idioma (gettext).
- **Documentación:** PHPDoc completo en DAOs/helpers.

## Contribución

1. Fork el repo.
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcion`).
3. Commit cambios (`git commit -m "Añade nueva funcion"`).
4. Push y abre PR.

**Estándares:** Usa PSR-12 para PHP, commits atómicos, tests para cambios.

## Licencia

MIT License - ver LICENSE si existe.

## Notas para Desarrolladores

- **Entorno:** Desarrollado en Windows/XAMPP. Ajusta rutas si usas Linux/Mac.
- **Errores comunes:** Verifica que MySQL esté corriendo y credenciales correctas. Si "Unknown database", importa el SQL.
- **Debugging:** Usa `var_dump` o Xdebug. Logs en `php_error.log` de XAMPP.
- **Dependencias:** Ninguna externa (solo PHP built-in + Bootstrap).
- **Versiones:** PHP 7.4+, MySQL 5.7+. Prueba en versiones superiores.

Para preguntas: Abre un issue en GitHub.