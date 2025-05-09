<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Swagger UI' }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('api-docs') }}/swagger-ui.css" >
    @if(!empty(settings()->favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage').'/'.settings()->favicon }}" sizes="16x16" />
    @else
        <link rel="icon" type="image/png" href="{{ asset('api-docs') }}/favicon-16x16.png" sizes="16x16" />
    @endif
    <style>
      html
      {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
      }

      *,
      *:before,
      *:after
      {
        box-sizing: inherit;
      }

      body
      {
        margin:0;
        background: #fafafa;
      }
    </style>
  </head>

    <body>  
        <div id="swagger-ui"></div>

        <script src="{{ asset('api-docs') }}/swagger-ui-bundle.js"> </script>
        <script src="{{ asset('api-docs') }}/swagger-ui-standalone-preset.js"> </script>
        <script>
            window.onload = function() {
                // Begin Swagger UI call region
                const ui = SwaggerUIBundle({
                    url: "api-docs/swagger.json",
                    dom_id: '#swagger-ui',
                    deepLinking: true,
                    presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                    ],
                    plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                    ],
                    layout: "StandaloneLayout",
                })
                // End Swagger UI call region

                window.ui = ui
            }
        </script>
    </body>
</html>
