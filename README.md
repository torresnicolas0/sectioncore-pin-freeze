# SectionCore Pin & Freeze

Plugin de la suite SectionCore para congelar HTML renderizado de bloques, entradas o páginas enteras.

## Rol en la suite

- Convertir renders dinámicos en HTML estático cuando hace falta estabilizar un bloque o una página.
- Bloquear la edición visual cuando el contenido está pineado.
- Capturar HTML desde el frontend o desde el estado del editor.

## Estado actual

- La vista previa del bloque pineado se renderiza en el canvas del editor.
- El inspector guarda historial por bloque con las últimas 5 versiones aplicadas.
- La acción de borrar borrador se retiró para simplificar el flujo.
- Las acciones de lista muestran solo `Pinear` o `Despinear` según el estado.
- El panel de post/page mantiene el flujo de captura y el historial de pines.

## Requisitos

- WordPress 5.2+
- PHP 7.2+

## Uso

1. Instalar y activar el plugin.
2. Abrir una entrada o página en Gutenberg.
3. Pinear un bloque individual desde el inspector o pinear el contenido completo desde el panel del documento.

## Desarrollo

```bash
npm install
npm run build
```

## Archivos principales

- `sectioncore-pin-freeze.php` bootstrap y runtime hooks.
- `includes/settings-page.php` pantalla de ajustes.
- `includes/ajax-fetcher.php` captura del frontend.
- `includes/history-manager.php` historial de snapshots.
- `docs/BLOCK_PIN_RENDER_CAPTURE.md` nota técnica de captura.

## Documentación relacionada

- [Ayuda para arquitecto](https://github.com/torresnicolas0/sectioncore-pin-freeze/blob/main/docs/architect-help.md)
- [Ayuda básica del sitio](https://github.com/torresnicolas0/sectioncore-pin-freeze/blob/main/docs/site-help.md)
