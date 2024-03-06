<?php
$link = "./sentiment_reviews.json";
$jsonData = file_get_contents($link);
$data = json_decode($jsonData, true);

function sortByConfidence($a, $b) {
    if ($a['confidence'] == $b['confidence']) return 0;
    return ($a['confidence'] < $b['confidence']) ? -1 : 1;
}

$reviews_before = $data['before_reviews'];
$reviews_after = $data['after_reviews'];

usort($reviews_before, 'sortByConfidence');
usort($reviews_after, 'sortByConfidence');

$dataPoints = [];
foreach ($reviews_before as $before) {
    if ($before['sentiment']=='positive'|| $before['sentiment']=='negative'){
        $dataPoints[] = ["x" => $before["review"], "y" => $before["confidence"]];
    }
}

$dataPointsAfter = [];
foreach ($reviews_after as $after) {
    if ($after['sentiment']=='positive'|| $after['sentiment']=='negative'){
        $dataPointsAfter[] = ["x" => $after["review"], "y" => $after["confidence"]];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Reviews</title>
    <link rel="icon" type="image/x-icon" href="./favicon.png">
    <link rel="stylesheet" href="./styles.css">
    <style>
        
    </style>
</head>

<header>
    <span>Sentiment Reviews</span>
</header>

<?
    $chart = $_GET['chart'];
?>

<script>
    window.onload = function() {

        const ctx = document.getElementById('before-chart');

        new Chart(ctx, {
            type: 'bar',
            data: {
            labels: [''],
            datasets: [{
                label: 'Before Reviews (<?= count($reviews_before) ?>)',
                data: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>,
                borderWidth: 1
            }]
            },
            options: {
            scales: {
                x: {
                display: false,
                },
                y: {
                beginAtZero: true
                }
            },
            plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 14,
                                    family: 'Poppins'
                                }
                            }
                        }
                    }  
            },   
        });

        const ctx2 = document.getElementById('after-chart');

        new Chart(ctx2, {
            type: 'bar',
            data: {
            labels: [''],
            datasets: [{
                label: 'After Reviews (<?= count($reviews_after) ?>)',
                data: <?php echo json_encode($dataPointsAfter, JSON_NUMERIC_CHECK); ?>,
                borderWidth: 1
            }]
            },
            options: {
            scales: {
                x: {
                display: false
                },
                y: {
                beginAtZero: true
                }
            },
            plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: 14,
                                    family: 'Poppins'
                                }
                            }
                        }
                    }  
            },   
        });

        ctx.render();
        ctx2.render();
    }
</script>

<body>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <? if ($chart == 'before') {
            echo 
            "<div id='chartContainerLarge' style='padding: 1rem; height: auto;'>
                <canvas id='before-chart' style='width:fit-content'></canvas>
            </div>";
        } else {
            echo 
            "<div id='chartContainer2Large' style='padding: 1rem; height: auto'>
                <canvas id='after-chart' style='width:fit-content'></canvas>
            </div>";
        } 
    ?>   
</body>

</html>