# Backend Technical Test — PHP

## Descripcion
Este repositorio contiene un API REST sencilla desarrollada a partir de PHP puro sin el uso de frameworks.
## Levantar el proyecto
Para poder levantarlo es nesesario contar con un archivo **.env** con todas las variables nesesarias, se puede tomar el .env.example aunque se puede modificar para montar con los puertos que se desee posteriormente solo se nesecita ejecutar **docker compose up -d --build** y esperar a que se monte el sistema.

Para ejecutar las pruebas aunque son un pocas por falta de tiempo se puede hacer ejecutando el comando **php test.php** desde la consola del contenedor de PHP

## Arquitectura
### Conteiners
Este repositorio esta construido sobre 4 contenedores comunicados a travez de una red llamada *api_network*.
- **mysql**: Este es el encargado de montar la base de datos.
- **PHPMyAdmin**: Este da una vista mas amigable a la informacion proporcionada por la base de datos de mysql
- **PHP**: En este contenedor se monta la aplicacion sin exponer puertos.
- **nginx**: Funciona como servidor web y es el encargado de exponer el API de PHP
### Puertos
Para exponer los contenedores se nesecita crear el archivo **.env** este proporciona las credenciales de mysql y los puertos en los que se expone el sistema, se puede hacer usoo del .env.example.

## Estructura del proyecto
El proyecto consta de varias capas que se descriviran a continuacion.

### Insercion de datos
En el folder de **./docker/php** se encuentra el Dockerfile encargado de la configuracion del servidor PHP en este mismo se ejecutan 2 archivos **migrate.php** y **seed.php** para ejecutar migraciones y seeders de la base de datos contiene unos cuantos registros a falta de herramientas como factory.

### public
mientras que en el folder **public** podemos encontrar el index.php core de la app es el encargado de montar el router y el autoload para poder trabajar con namespaces asi mismo el router es proporcionado por los modulos correspondientes.

### APP
Dentro de app se encuentra practicamente todo el sistema.
- **DATABASE**: En este podemos encontrar todo lo relacionado a las bases de datos desde *Migraciones* y *Seeders* como *Schemas* que se encarga de hacer todas las consultas SQL en dado caso de que un dia se cambie de provedor unicamente se actualizan esos archivos.

- **Logs**: Encargados de dar observabilidad un poco basicos se encargan de moniteorear el timing que pasa en toda la ruta, el registro de requests asignandoles un identificador rustico y el registro de errores.

- **Src**: Source code de la plataforma en este folder encontramos los metodos **Core** que son la base del sistema como la coneccion con la DB, metodos Request y Response y el Router global donde se definen las peticiones entrantes.
Ademas encontramos los **modules** correspondientes a cada feature donde se encuentra toda su logica de negocio, *Controlers, DTOs, Classess, Actions, Services y Router*.

## API
El API cuenta con los siguientes endpoints como se solicito
- `POST /products`: Permite insertar 1 producto.
- `GET /products`: Trae una lista de productos.
- `POST /orders`: Permite insertar 1 order.
- `PATCH /orders/{id}/confirm`: Permite actualizar el status del order apuntado y resta los items de los products si el stock alcanza.
- `PATCH /orders/{id}/cancel`: Cancela el order
- `GET /orders/{id}`: Obtiene 1 solo order junto a sus items.
- `GET /orders`: Obtiene una lista de orders paginada a la que se pueden aplicar filtros de *status y customer_id*.
## Decisiones técnicas
### MySQLi
Libreria empleada para conectar con MySQL elemento que permitio tener control directo sobre las consultas y el como llamarlas.

### Arquitectura modular
Se opto por organizarlo por modulos buscando mantener separadas las responsabilidades y facilitar el escalado del proyecto.

### DTOs
Se opto por su uso para mantener una consistencia en la informacion procesada por las capas facilitando validacion y limpieza de datos.

### Actions y Services
Los **Actions** Se encargan de cordinar los casos de uso, en cuanto a los **Servicios** estos contienen la logica de negocio y son especiales para realizar una unica tarea.

### Transacciones
Empleadas para la creacion y aprovacion de orders garantisando que las operaciones sean atomicas.

Si alguna de las operaciones falla se realiza un *ROLLBACK* mientras que si todas las operaciones terminan correctamente se realiza un *COMMIT*.

### Router propio
Se realizo un router sencillo para manejar los metodos HTTP y las rutas del API sin la dependencia de un framework externo a mi parecer la parte mas compleja.

### Migraciones y Seeders
Ademas de implementar otro sistema sencillo para el manejo de migraciones y seeders creando la estructura de la DB.
### Docker
Se utilizo Docker para facilitar la configuracion y ejecucion del proyecto evitando depender de una configuración especifica.
### Logging
Se creo un sistema de logging para registrar errores, requests y tiempos de carga, facilitando la identificacion de problemas y el seguimiento del comportamiento de la API.
### Testing
Se creo un archivo de pruebas para ejecutar manualmente diferentes escenarios de la API, incluyendo casos exitosos y casos donde los datos enviados son inválidos o incompletos.
