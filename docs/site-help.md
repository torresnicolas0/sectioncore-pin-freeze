# Pin & Freeze

## Resumen
Pin & Freeze ayuda a fijar selectores y estados estables sobre elementos del sitio. Sirve para sostener comportamientos previsibles cuando el diseño o el contenido cambian con el tiempo.

![Vista general de Pin & Freeze](assets/pin-freeze-overview.png)

## Qué podés hacer desde esta pantalla
- Definir el tipo de selector base que conviene capturar.
- Guardar criterios consistentes para futuras capturas.
- Mantener una estrategia de selección estable entre versiones del sitio.

## Configuración recomendada por defecto
| Área | Recomendación | Motivo |
| --- | --- | --- |
| Selector base | Usar el tipo más estable disponible | Reduce roturas cuando cambia el layout. |
| Revisión | Revalidar el selector después de cambios fuertes de theme o markup | Evita referencias obsoletas. |
| Alcance | Capturar sólo lo necesario | Mantiene el sistema más claro y mantenible. |

## Paso a paso recomendado
1. Abrí `SectionCore > Pin & Freeze`.
2. Elegí el tipo de selector base más estable para tu caso.
3. Guardá la configuración.
4. Revisá el comportamiento real en la pantalla o bloque donde lo vayas a usar.
5. Si el markup cambia, volvé a validar el selector antes de seguir trabajando.

## Ejemplo completo
Después de ajustar un bloque destacado de `Inmobiliaria SectionCore`, podés usar un selector más estable para evitar que una pequeña refactorización del HTML invalide la referencia capturada.

## Errores frecuentes y cómo evitarlos
> **Error frecuente:** elegir un selector frágil basado en una estructura temporal del DOM.  
> **Cómo evitarlo:** priorizá selectores claros y estables antes que rutas demasiado específicas.
>
> **Error frecuente:** no volver a revisar el selector tras un cambio de theme.  
> **Cómo evitarlo:** cada vez que cambie el render o la jerarquía visual, validá otra vez la captura.

## Validación final esperada
- El selector base elegido sigue apuntando al objetivo correcto.
- La configuración guardada se entiende y se sostiene en el tiempo.
- No dependés de rutas frágiles o accidentales del DOM.

## Siguiente paso
Antes de desbloquear más bloques o exportar el sitio, conviene revisar el estado de [Credits](help://credits).
