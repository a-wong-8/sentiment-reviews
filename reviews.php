<!DOCTYPE html>
<html lang="en">

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

$before_pos = 0;
$before_neg = 0;
$before_neu = 0;

$after_pos = 0;
$after_neg = 0;
$after_neu = 0;

$dataPoints = [];
foreach ($reviews_before as $before) {
    if ($before['sentiment']=='positive'|| $before['sentiment']=='negative'){
        $dataPoints[] = ["x" => $before["review"], "y" => $before["confidence"]];
    }

    if ($before['sentiment'] == 'positive') {
        $before_pos += 1;
    } elseif ($before['sentiment'] == 'negative') {
        $before_neg += 1;
    } else {
        $before_neu += 1;
    }
}

$dataPointsAfter = [];
foreach ($reviews_after as $after) {
    if ($after['sentiment']=='positive'|| $after['sentiment']=='negative'){
        $dataPointsAfter[] = ["x" => $after["review"], "y" => $after["confidence"]];
    }

    if ($after['sentiment'] == 'positive') {
        $after_pos += 1;
    } elseif ($after['sentiment'] == 'negative') {
        $after_neg += 1;
    } else {
        $after_neu += 1;
    }
}
?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Reviews</title>
    <link rel="icon" type="image/x-icon" href="./favicon.png">
    <link rel="stylesheet" href="./styles.css">

    <link href="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.8/b-2.4.2/b-html5-2.4.2/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.8/b-2.4.2/b-html5-2.4.2/datatables.min.js"></script>

    <script src="https://cdn.datatables.net/plug-ins/1.13.7/pagination/input.js"></script>
</head>

    <script>
        $(document).ready(function() {

            $('#before-reviews-table').DataTable({
                order: [
                [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "pagingType": "input"
            });

            $('#after-reviews-table').DataTable({
                order: [
                [1, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "pagingType": "input"
            });

        });
    </script>

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
                        beginAtZero: true,
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

            ctx.render();
            ctx2.render();
        }
    </script>
    <style>
  
    </style>
</head>

<header>
    <span>Sentiment Reviews</span>
</header>

<body>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <section class="charts">    
        <a href="./large_chart.php?chart=before">
            <div id="chartContainer" style="padding: 1rem; height: auto;">
            <canvas id="before-chart" style="width:fit-content"></canvas>
            </div>
        </a>
        <a href="./large_chart.php?chart=after">
            <div id="chartContainer2" style="padding: 1rem; height: auto;">
            <canvas id="after-chart" style="width:fit-content"></canvas>
            </div>
        </a>
    </section>

    <? $total_after = ($after_pos + $after_neg + $after_neu); ?>
    <? $total_before = ($before_pos + $before_neg + $before_neu); ?>

    <section class="stats">
        <table id='stats-table' class='display' width='80%'>
            <h2>Quick Stats</h2>
            <thead>
                <tr>
                    <th></th>
                    <th><b>Positive</b></th>
                    <th><b>% Positive</b></th>
                    <th><b>Negative</b></th>
                    <th><b>% Negative</b></th>
                    <th><b>Other*</b></th>
                    <th><b>% Other</b></th>
                    <th><b>Total</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>
                            Before
                    </td></b>
                    <td><?= $before_pos ?></td>
                    <td><?= (number_format(sprintf("%.2f", $before_pos / ($total_before) * 100), 1)) . ' %' ?></td>
                    <td><?= $before_neg ?></td>
                    <td><?= (number_format(sprintf("%.2f", $before_neg / ($total_before) * 100), 1)) . ' %' ?></td>
                    <td><?= $before_neu ?></td>
                    <td><?= (number_format(sprintf("%.2f", $before_neu / ($total_before) * 100), 1)) . ' %' ?></td>
                    <td><?= $total_before ?></td>
                </tr>
                <tr>
                    <td><b>
                            After
                    </td></b>
                    <td><?= $after_pos ?></td>
                    <td><?= (number_format(sprintf("%.2f", $after_pos / ($total_after) * 100), 1)) . ' %' ?></td>
                    <td><?= $after_neg ?></td>
                    <td><?= (number_format(sprintf("%.2f", $after_neg / ($total_after) * 100), 1)) . ' %' ?></td>
                    <td><?= $after_neu ?></td>
                    <td><?= (number_format(sprintf("%.2f", $after_neu / ($total_after) * 100), 1)) . ' %' ?></td>
                    <td><?= ($total_after) ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?= ($after_pos + $before_pos) ?></th>
                    <th></th>
                    <th><?= ($after_neg + $before_neg) ?></th>
                    <th></th>
                    <th><?= ($after_neu + $before_neu) ?></th>
                    <th></th>
                    <th><?= ($total_after + $total_before) ?></th>
                </tr>
            </tfoot>
        </table>
        <span>*Other includes Mixed and Neutral reviews</span>
    </section>

    <section class="before-reviews">
        <h2>Before Reviews</h2>

        <div id="buttons">
            <input type="checkbox" class="searchButtonBefore" id="checkboxPos" data-value="Positive"></input>
            <label for="checkboxPos" class="searchButtonLabel">Positive</label>

            <input type="checkbox" class="searchButtonBefore" id="checkboxNeg"  data-value="Negative"></input>
            <label for="checkboxNeg" class="searchButtonLabel">Negative</label>

            <input type="checkbox" class="searchButtonBefore" id="checkboxNeu"  data-value="neutral"></input>
            <label for="checkboxNeu" class="searchButtonLabel">Neutral</label>

            <input type="checkbox" class="searchButtonBefore" id="checkboxMix"  data-value="Mixed"></input>
            <label for="checkboxMix" class="searchButtonLabel">Mixed</label>
        </div>

        <table id='before-reviews-table' class='display' width='95%'>
            <thead>
                <tr>
                    <th></th>
                    <th>Sentiment</th>
                    <th>Confidence</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                foreach ($reviews_before as $before) {
                    if ($before['confidence'] != 1.1) {
                        echo "<tr>";
                        echo "<td>" . ($count += 1) . "</td>";
                        echo "<td>" . ucfirst($before['sentiment']) . "</td>";
                        echo "<td>{$before['confidence']}</td>";
                        echo "<td>{$before['review']}</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </section>

    <section class="after-reviews">
        <h2>After Reviews</h2>
        <div id="buttons">
            <input type="checkbox" class="searchButton" id="checkboxPosBefore" data-value="Positive"></input>
            <label for="checkboxPosBefore" class="searchButtonLabel">Positive</label>

            <input type="checkbox" class="searchButton" id="checkboxNegBefore"  data-value="Negative"></input>
            <label for="checkboxNegBefore" class="searchButtonLabel">Negative</label>

            <input type="checkbox" class="searchButton" id="checkboxNeuBefore"  data-value="neutral"></input>
            <label for="checkboxNeuBefore" class="searchButtonLabel">Neutral</label>

            <input type="checkbox" class="searchButton" id="checkboxMixBefore"  data-value="Mixed"></input>
            <label for="checkboxMixBefore" class="searchButtonLabel">Mixed</label>
        </div>

        <table id='after-reviews-table' class='display' width='95%'>
            <thead>
                <tr>
                    <th></th>
                    <th>Sentiment</th>
                    <th>Confidence</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                foreach ($reviews_after as $after) {
                    if ($after['confidence'] != 1.1) {
                        echo "<tr>";
                        echo "<td>" . ($count += 1) . "</td>";
                        echo "<td>" . ucfirst($after['sentiment']) . "</td>";
                        echo "<td>{$after['confidence']}</td>";
                        echo "<td>{$after['review']}</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </section>
</body>

<script>
    var selectedSearches = [];
    $('.searchButton').on('click', function() {
        var searchValue = $(this).data('value');

        if (selectedSearches.includes(searchValue)) {
            selectedSearches = selectedSearches.filter(value => value !== searchValue);
        } else {
            selectedSearches.push(searchValue);
        }
        applySearch();
    });

    function applySearch() {
        var table = $('#after-reviews-table').DataTable();
        var searchRegex = selectedSearches.map(value => '^' + $.fn.dataTable.util.escapeRegex(value) + '$').join('|');
        return table.column(1).search(searchRegex, true, false).draw();
    }
    // for before review tables ---------------------------------
    var selectedSearchesBefore = [];
    $('.searchButtonBefore').on('click', function() {
        var searchValue = $(this).data('value');

        if (selectedSearchesBefore.includes(searchValue)) {
            selectedSearchesBefore = selectedSearchesBefore.filter(value => value !== searchValue);
        } else {
            selectedSearchesBefore.push(searchValue);
        }
        applySearchBefore();
    });

    function applySearchBefore() {
        var table = $('#before-reviews-table').DataTable();
        var searchRegex = selectedSearchesBefore.map(value => '^' + $.fn.dataTable.util.escapeRegex(value) + '$').join('|');
        return table.column(1).search(searchRegex, true, false).draw();
    } 
</script>

</html>