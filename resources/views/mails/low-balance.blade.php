  <!DOCTYPE html>
        <html lang="es">
        <head>
          <meta charset="UTF-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Georgia,serif;">

          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
            <tr>
              <td align="center">
                <table width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

                  <!-- Header con color del hotel -->
                  <tr>
                    <td style="background: #284433;padding:32px 40px;text-align:center;">
                      <p style="margin:0;color: #ffffff;font-size:18px;letter-spacing:4px;text-transform:uppercase; font-weight: 700;"></p>
                      <h1 style="margin:10px 0 0;color: #b3ddc2;font-size:20px;font-weight:700;letter-spacing:1px;">Proyecto: {{ $projectName }}</h1>
                    </td>
                  </tr>

                  <!-- Body -->
                  <tr>
                    <td style="padding:40px;">
                      <p style="margin:0 0 24px;color:#555;font-size:15px;line-height:1.6;">
                       se esta acabando el saldo :(
                      </p>

                      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9f9;border-left:4px solid #38513d;border-radius:4px;">
                        <tr>
                          <td style="padding:24px 28px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                              <tr>
                                <td style="padding:10px 0;border-bottom:1px solid #eee;">
                                  <span style="color:#999;font-size:11px;letter-spacing:2px;text-transform:uppercase;">BALANCE ACTUAL</span><br/>
                                  <span style="color:#1a1a1a;font-size:16px;">{{ $projectCurrency }}{{ $balance }}</span>
                                </td>
                              </tr>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>

                      <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
                        <tr>
                          <td align="center">
                            <a href="{{ url('/finance/' . $projectId) }}"
                              style="display:inline-block;background: #3c5647;color:#ffffff;font-size:13px;letter-spacing:2px;text-transform:uppercase;text-decoration:none;padding:14px 32px;border-radius:4px;">
                                Ver Proyecto
                            </a>
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>

                  <!-- Footer -->
                  <tr>
                    <td style="background:#f9f9f9;padding:20px 40px;border-top:1px solid #eee;text-align:center;">
                      <p style="margin:0 0 4px;color:#aaa;font-size:11px;letter-spacing:1px;">
                                        Mensaje generado automáticamente por el sistema. No responder a este correo.

                      </p>
                      <p style="margin:0;color:#aaa;font-size:11px;letter-spacing:1px;">
                        <a href="{{ url('/finance') }}">Ir ala intranet</a>
                      </p>
                    </td>
                  </tr>

                </table>
              </td>
            </tr>
          </table>

        </body>
        </html>