# Remediacion de alertas OWASP ZAP

## Objetivo

Reducir a cero las alertas que representan riesgo en el despliegue local de PichangasYa: nivel alto, medio y bajo. Las observaciones informativas se conservan porque describen caracteristicas detectadas y no son vulnerabilidades.

## Comparacion de resultados

| Medicion | Alto | Medio | Bajo | Informativo |
|---|---:|---:|---:|---:|
| Analisis activo inicial | 0 | 3 | 6 | 4 |
| Analisis activo despues de la remediacion | 0 | 0 | 1* | 5 |
| Verificacion final en Apache | 0 | 0 | 0 | 2 |

\* La alerta baja de la medicion intermedia correspondia al servidor PHP temporal usado exclusivamente para aislar la prueba. El despliegue real en Apache entrega correctamente `X-Content-Type-Options: nosniff`, por lo que la verificacion final no presenta esa alerta.

## Medidas aplicadas

| Hallazgo | Medida de remediacion |
|---|---|
| Content Security Policy inexistente o permisiva | Politica CSP restrictiva con un `nonce` aleatorio por respuesta para scripts y estilos autorizados. |
| Proteccion contra clickjacking | `frame-ancestors 'none'` y `X-Frame-Options: DENY`. |
| Dependencias externas y falta de SRI | Bootstrap, jQuery, iconos y notificaciones se sirven desde recursos locales. |
| Ausencia de `nosniff` | Cabecera `X-Content-Type-Options: nosniff` desde Laravel y Apache. |
| Divulgacion de tecnologia | Eliminacion de `X-Powered-By`; Apache configurado con `ServerTokens Prod` y `ServerSignature Off`. |
| Cookie XSRF accesible desde JavaScript | Se deshabilito la cookie XSRF innecesaria; los formularios mantienen el token CSRF protegido. |
| Redirecciones grandes en endpoints de autenticacion | Respuestas JSON uniformes para los flujos evaluados. |
| Error 500 en recuperacion de contrasena | Se incorporo el layout faltante y una respuesta generica que evita enumeracion de usuarios. |
| Politicas auxiliares ausentes | Se agregaron `Referrer-Policy`, `Permissions-Policy` y `X-Permitted-Cross-Domain-Policies`. |

## Interpretacion correcta

El resultado final es **cero alertas de riesgo**: 0 altas, 0 medias y 0 bajas. ZAP mantiene dos registros informativos: identificacion de una aplicacion web moderna e identificacion del mecanismo de sesion. Eliminarlos o excluirlos del informe solo para mostrar un total absoluto de cero no seria una correccion de seguridad.

## Evidencias

- `REPORTE_ZAP.html`: informe activo inicial.
- `REPORTE_ZAP_FINAL.html`: repeticion activa despues de las correcciones.
- `REPORTE_ZAP_FINAL_0_RIESGOS.html`: verificacion final oficial contra Apache.
- `EVIDENCIA_ZAP_0_RIESGOS.png`: captura de la tabla resumen final.
- `zap-final-plan.yaml`: plan reproducible usado por el Automation Framework de ZAP.
