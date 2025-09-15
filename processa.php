<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="modacasualdeluxo.css">
</head>

<body>
    <div class="organizador">
        <div class="menu">
            <div id="ghost">
                <div id="red">
                    <div id="pupil"></div>
                    <div id="pupil1"></div>
                    <div id="eye"></div>
                    <div id="eye1"></div>
                    <div id="top0"></div>
                    <div id="top1"></div>
                    <div id="top2"></div>
                    <div id="top3"></div>
                    <div id="top4"></div>
                    <div id="st0"></div>
                    <div id="st1"></div>
                    <div id="st2"></div>
                    <div id="st3"></div>
                    <div id="st4"></div>
                    <div id="st5"></div>
                    <div id="an1"></div>
                    <div id="an2"></div>
                    <div id="an3"></div>
                    <div id="an4"></div>
                    <div id="an5"></div>
                    <div id="an6"></div>
                    <div id="an7"></div>
                    <div id="an8"></div>
                    <div id="an9"></div>
                    <div id="an10"></div>
                    <div id="an11"></div>
                    <div id="an12"></div>
                    <div id="an13"></div>
                    <div id="an14"></div>
                    <div id="an15"></div>
                    <div id="an16"></div>
                    <div id="an17"></div>
                    <div id="an18"></div>
                </div>
                <div id="shadow"></div>
            </div>

            <nav class="menu-links">
                <a href="mainpage.html"><span>Multimídia e Hipermídia e o Uso de Inteligência Artificial na Geração de Imagens
                    </span></a>
                <a href="#"><span>Processo de formação e processamento de imagens</span></a>
            </nav>

        </div>

        <div class="conteudo">

            <div class="blog-conteudo"
                style="max-width: 900px; margin: auto; font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;">


                <?php


                // Função para obter informações básicas da imagem
                function obterInformacoes($arquivo, $nome)
                {
                    $info = getimagesize($arquivo);
                    return [
                        "nome"    => $nome,
                        "largura" => $info[0],
                        "altura"  => $info[1],
                        "tipo"    => $info['mime']
                    ];
                }

                // Função para obter dados EXIF (quando disponível)
                function obterExif($arquivo, $tipo)
                {
                    if (function_exists('exif_read_data') && $tipo == "image/jpeg") {
                        $exif = exif_read_data($arquivo);
                        return $exif ?: [];
                    }
                    return [];
                }

                // Função para criar recurso de imagem dependendo do tipo
                function criarRecursoImagem($arquivo, $tipo)
                {
                    switch ($tipo) {
                        case "image/jpeg":
                            return imagecreatefromjpeg($arquivo);
                        case "image/png":
                            return imagecreatefrompng($arquivo);
                        case "image/gif":
                            return imagecreatefromgif($arquivo);
                        default:
                            return null;
                    }
                }

                // Função para converter para escala de cinza
                function converterPretoBranco($img)
                {
                    $copy = $img;
                    imagefilter($copy, IMG_FILTER_GRAYSCALE);
                    ob_start();
                    imagejpeg($copy);
                    return base64_encode(ob_get_clean());
                }

                // Função para gerar thumbnail
                function criarThumbnail($img, $largura, $altura, $nova_largura = 150)
                {
                    $nova_altura = (int)(($altura / $largura) * $nova_largura);
                    $thumb = imagecreatetruecolor($nova_largura, $nova_altura);
                    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura, $altura);

                    ob_start();
                    imagejpeg($thumb);
                    $thumbData = base64_encode(ob_get_clean());

                    imagedestroy($thumb);
                    return $thumbData;
                }

                // ===================== //
                // EXECUÇÃO DO SCRIPT
                // ===================== //

                if (isset($_FILES['imagem'])) {
                    $arquivo = $_FILES['imagem']['tmp_name'];
                    $nome    = $_FILES['imagem']['name'];

                    // 1. Informações da Imagem
                    $info = obterInformacoes($arquivo, $nome);
                    echo "<h2>Informações da Imagem</h2>";
                    echo "Nome do arquivo: " . htmlspecialchars($info['nome']) . "<br>";
                    echo "Tipo MIME: " . $info['tipo'] . "<br>";
                    echo "Largura: " . $info['largura'] . " px<br>";
                    echo "Altura: " . $info['altura'] . " px<br>";

                    // 2. Metadados EXIF
                    $exif = obterExif($arquivo, $info['tipo']);
                    if (!empty($exif)) {
                        echo "<h3>Metadados EXIF:</h3>";
                        if (isset($exif['Make'])) echo "Câmera: " . $exif['Make'] . "<br>";
                        if (isset($exif['Model'])) echo "Modelo: " . $exif['Model'] . "<br>";
                        if (isset($exif['DateTime'])) echo "Data da Foto: " . $exif['DateTime'] . "<br>";
                    } else {
                        echo "<h3>Nenhum dado EXIF encontrado.</h3>";
                    }

                    // 3. Pré-visualização Original
                    echo "<h3>Pré-visualização Original:</h3>";
                    echo "<img src='data:{$info['tipo']};base64," . base64_encode(file_get_contents($arquivo)) . "' style='max-width:400px;'><br><br>";

                    // Criar recurso de imagem
                    $img = criarRecursoImagem($arquivo, $info['tipo']);
                    if ($img === null) {
                        die("Formato de imagem não suportado!");
                    }

                    // 4. Imagem em Preto e Branco
                    $grayImage = converterPretoBranco($img);
                    echo "<h3>Imagem em Preto e Branco:</h3>";
                    echo "<img src='data:image/jpeg;base64,$grayImage' style='max-width:400px;'><br><br>";

                    // 5. Miniatura (Thumbnail)
                    $thumbImage = criarThumbnail($img, $info['largura'], $info['altura']);
                    echo "<h3>Miniatura (Thumbnail):</h3>";
                    echo "<img src='data:image/jpeg;base64,$thumbImage' style='max-width:400px;'><br>";

                    // Liberar memória
                    imagedestroy($img);
                } else {
                    echo "Nenhuma imagem enviada.";
                }

                ?>
                          <a class="Btn" href="index.html">voltar</a>
            </div>
        </div>
    </div>
</body>

</html>
