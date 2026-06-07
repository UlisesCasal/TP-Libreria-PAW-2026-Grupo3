<?php

namespace PAW\App\Controllers;

class ApiController
{
    public function buscarPorIsbn()
    {
        header('Content-Type: application/json');

        $isbn = trim($_GET['isbn'] ?? '');
        if (empty($isbn)) {
            http_response_code(400);
            echo json_encode(['error' => 'ISBN requerido']);
            return;
        }

        // Normalizar ISBN: sacar guiones y espacios
        $isbnLimpio = preg_replace('/[^0-9X]/i', '', $isbn);

        // 1. Obtener datos de la edición por ISBN
        $url = "https://openlibrary.org/isbn/{$isbnLimpio}.json";
        $context = stream_context_create(['http' => [
            'header' => "User-Agent: TPLibreriaPAW (tp-libreria@example.com)\r\n"
        ]]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            http_response_code(404);
            echo json_encode(['error' => 'No se encontró un libro con ese ISBN']);
            return;
        }

        $data = json_decode($response, true);

        // 2. Mapear campos básicos
        $result = [
            'titulo'    => $data['title'] ?? '',
            'autor'     => '',
            'genero'    => '',
            'fechapub'  => $this->parseDate($data['publish_date'] ?? ''),
            'paginas'   => $data['number_of_pages'] ?? '',
            'descr'     => '',
            'cover_url' => '',
        ];

        // 3. Obtener nombre del autor
        if (!empty($data['authors'][0]['key'])) {
            $authorUrl = "https://openlibrary.org{$data['authors'][0]['key']}.json";
            $authorResponse = @file_get_contents($authorUrl, false, $context);
            if ($authorResponse) {
                $authorData = json_decode($authorResponse, true);
                $result['autor'] = $authorData['name'] ?? '';
            }
        }

        // 4. Obtener descripción y géneros desde la obra (work)
        if (!empty($data['works'][0]['key'])) {
            $workUrl = "https://openlibrary.org{$data['works'][0]['key']}.json";
            $workResponse = @file_get_contents($workUrl, false, $context);
            if ($workResponse) {
                $workData = json_decode($workResponse, true);

                // Descripción
                if (!empty($workData['description'])) {
                    $result['descr'] = is_array($workData['description'])
                        ? ($workData['description']['value'] ?? '')
                        : $workData['description'];
                }

                // Géneros / subjects
                if (!empty($workData['subjects'])) {
                    $subjects = $workData['subjects'];
                    if (is_array($subjects)) {
                        $result['genero'] = implode(', ', array_slice($subjects, 0, 4));
                    }
                }
            }
        }

        // 5. URL de la tapa
        if (!empty($data['covers'][0])) {
            $result['cover_url'] = "https://covers.openlibrary.org/b/id/{$data['covers'][0]}-L.jpg";
        } else {
            // Fallback: intentar con ISBN
            $result['cover_url'] = "https://covers.openlibrary.org/b/isbn/{$isbnLimpio}-L.jpg";
        }

        echo json_encode($result);
    }

    public function buscarLibro()
    {
        header('Content-Type: application/json');

        $q = trim($_GET['q'] ?? '');
        if (empty($q) || strlen($q) < 3) {
            echo json_encode([]);
            return;
        }

        $url = "https://openlibrary.org/search.json?q=" . urlencode($q) . "&limit=6";

        $context = stream_context_create(['http' => [
            'header' => "User-Agent: TPLibreriaPAW (tp-libreria@example.com)\r\n"
        ]]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            echo json_encode([]);
            return;
        }

        $data = json_decode($response, true);
        $results = [];

        if (!empty($data['docs'])) {
            foreach ($data['docs'] as $doc) {
                $isbn = '';
                if (!empty($doc['isbn'])) {
                    // Tomar el primer ISBN-13 o ISBN-10
                    $isbn = is_array($doc['isbn']) ? $doc['isbn'][0] : $doc['isbn'];
                }

                $coverUrl = '';
                if (!empty($doc['cover_i'])) {
                    $coverUrl = "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-L.jpg";
                } elseif (!empty($isbn)) {
                    $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
                }

                $results[] = [
                    'titulo'   => $doc['title'] ?? '',
                    'autor'    => !empty($doc['author_name'][0]) ? $doc['author_name'][0] : '',
                    'genero'   => !empty($doc['subject'][0]) ? implode(', ', array_slice($doc['subject'], 0, 3)) : '',
                    'fechapub' => !empty($doc['first_publish_year'])
                        ? $doc['first_publish_year'] . '-01-01'
                        : '',
                    'paginas'  => $doc['number_of_pages_median'] ?? '',
                    'isbn'     => $isbn,
                    'cover_url' => $coverUrl,
                    'key'      => $doc['key'] ?? '',
                ];
            }
        }

        echo json_encode($results);
    }

    public function detalleLibro()
    {
        header('Content-Type: application/json');

        $workKey = trim($_GET['key'] ?? '');
        if (empty($workKey)) {
            http_response_code(400);
            echo json_encode(['error' => 'Key requerida']);
            return;
        }

        $context = stream_context_create(['http' => [
            'header' => "User-Agent: TPLibreriaPAW (tp-libreria@example.com)\r\n"
        ]]);

        // 1. Obtener la obra
        $workUrl = "https://openlibrary.org{$workKey}.json";
        $workResponse = @file_get_contents($workUrl, false, $context);
        if ($workResponse === false) {
            http_response_code(404);
            echo json_encode(['error' => 'No se encontró la obra']);
            return;
        }

        $workData = json_decode($workResponse, true);
        if (empty($workData)) {
            http_response_code(404);
            echo json_encode(['error' => 'Datos de obra vacíos']);
            return;
        }

        $result = [
            'titulo'    => $workData['title'] ?? '',
            'autor'     => '',
            'genero'    => '',
            'fechapub'  => '',
            'paginas'   => '',
            'isbn'      => '',
            'descr'     => '',
            'cover_url' => '',
        ];

        // Descripción
        if (!empty($workData['description'])) {
            $result['descr'] = is_array($workData['description'])
                ? ($workData['description']['value'] ?? '')
                : $workData['description'];
        }

        // Géneros
        if (!empty($workData['subjects'])) {
            $subjects = $workData['subjects'];
            if (is_array($subjects)) {
                $result['genero'] = implode(', ', array_slice($subjects, 0, 4));
            }
        }

        // Autor
        if (!empty($workData['authors'][0]['author']['key'])) {
            $authorKey = $workData['authors'][0]['author']['key'];
            $authorUrl = "https://openlibrary.org{$authorKey}.json";
            $authorResponse = @file_get_contents($authorUrl, false, $context);
            if ($authorResponse) {
                $authorData = json_decode($authorResponse, true);
                $result['autor'] = $authorData['name'] ?? '';
            }
        }

        // 2. Buscar la primera edición que tenga ISBN y páginas
        if (!empty($workData['works'])) {
            // Si workData tiene key de works, usar eso
        }

        // Intentar obtener ediciones: la API de works tiene un endpoint /works/{key}/editions
        $editionsUrl = "https://openlibrary.org{$workKey}/editions.json?limit=5";
        $editionsResponse = @file_get_contents($editionsUrl, false, $context);
        if ($editionsResponse) {
            $editionsData = json_decode($editionsResponse, true);
            if (!empty($editionsData['entries'])) {
                foreach ($editionsData['entries'] as $entry) {
                    // Tomar la primera edición que tenga ISBN y páginas
                    if (empty($result['isbn']) && !empty($entry['isbn_13'][0])) {
                        $result['isbn'] = $entry['isbn_13'][0];
                    }
                    if (empty($result['paginas']) && !empty($entry['number_of_pages'])) {
                        $result['paginas'] = $entry['number_of_pages'];
                    }
                    if (empty($result['fechapub']) && !empty($entry['publish_date'])) {
                        $result['fechapub'] = $this->parseDate($entry['publish_date']);
                    }
                    if (empty($result['cover_url']) && !empty($entry['covers'][0])) {
                        $result['cover_url'] = "https://covers.openlibrary.org/b/id/{$entry['covers'][0]}-L.jpg";
                    }
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * Parsea una fecha de Open Library al formato YYYY-MM-DD.
     * Open Library devuelve cosas como "1999", "March 1999", "1999-03-01", etc.
     */
    private function parseDate(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        // Si ya viene en formato ISO (YYYY-MM-DD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Extraer año
        if (preg_match('/\b(\d{4})\b/', $date, $matches)) {
            return $matches[1] . '-01-01';
        }

        return '';
    }
}
