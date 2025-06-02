# Prueba Técnica Kuantaz

¡Bienvenido! Este repositorio contiene la solución a la prueba técnica de Kuantaz. A continuación, encontrarás toda la información relevante sobre el desafío, la arquitectura, el uso y la documentación de la API desarrollada.

## Descripción del Desafío

Se solicita implementar un endpoint que procese información proveniente de tres fuentes externas:

- **Beneficios:** Lista de beneficios recibidos por un usuario a lo largo de los años, cada uno con monto y fecha.
- **Filtros:** Información de cada beneficio, incluyendo montos mínimos, máximos y el id de la ficha asociada.
- **Fichas:** Detalles de cada ficha, asociada a un beneficio a través del filtro.

### Requerimientos del Endpoint

El endpoint debe retornar:

1. Beneficios ordenados por años.
2. Monto total por año.
3. número de beneficios por año.
4. Filtrar solo los beneficios que cumplan los montos máximos y mínimos.
5. Cada beneficio debe traer su ficha.
6. Se debe ordenar por año, de mayor a menor.

## Solución Propuesta

La solución implementa un endpoint RESTful en Laravel que:

- Consume los tres endpoints externos.
- Procesa y filtra los datos utilizando las potentes **Collections** de Laravel.
- Agrupa y ordena los beneficios por año, calculando totales y cantidades.
- Adjunta la información de la ficha a cada beneficio.
- Expone la documentación de la API mediante **Swagger**.
- Incluye pruebas unitarias y un archivo de colección para Postman.

## Instalación y Ejecución

1. **Clonar el repositorio:**

   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd pt-kuantaz
   ```

2. **Instalar dependencias:**

   ```bash
   composer install
   ```
3. **Levantar el servidor de desarrollo:**

   ```bash
   php artisan serve
   ```

## Documentación de la API

La documentación interactiva está disponible mediante **Swagger**. Accede a ella en:

```
http://pt-kuantaz.test/api/documentation
```

Aquí podrás explorar los endpoints, sus parámetros y respuestas.

## Pruebas Unitarias

Para ejecutar los tests:

```bash
php artisan test
```

## Colección de Postman

En la raíz del proyecto encontrarás el archivo `Kuantaz.postman_collection.json`.

### ¿Cómo usar la colección?

1. Abre Postman.
2. Haz clic en "Importar" y selecciona el archivo `Kuantaz.postman_collection.json`.
3. Una vez importada, podrás ver y ejecutar fácilmente las peticiones a los endpoints documentados.

Esto te permitirá probar rápidamente todas las funcionalidades de la API y validar los requisitos del desafío.

## Tecnologías Utilizadas

- **Laravel** (Framework PHP)
- **Swagger/OpenAPI** (Documentación de API)
- **PHPUnit** (Pruebas)
- **Postman** (Pruebas manuales de API)

## Estructura del Proyecto

- `app/Http/Controllers/Api/BeneficioController.php`: Lógica principal del endpoint solicitado.
- `routes/api.php`: Definición de rutas de la API.
- `tests/`: Pruebas unitarias y de integración.

## Consideraciones

- Se hace uso intensivo de las **Collections** de Laravel para un procesamiento eficiente y elegante de los datos.
- El endpoint está protegido contra errores en la obtención de datos externos.
- La documentación es clara y accesible para facilitar la integración y pruebas.

## Contacto

Para cualquier consulta o sugerencia, no dudes en contactarme.

---

¡Gracias por la oportunidad y el tiempo dedicado a revisar esta solución!

