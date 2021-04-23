<?php require_once('connect/conn.php'); ?>

<?php
session_start();

$month = $_SESSION['month'];
$year = $_SESSION['year'];
$id = $_SESSION["id"];

//  query database
//  Need apprentice name, apprentice occupation, supervisor info
$query = "SELECT concat(p.fname, ' ', p.lname) AS apprentice_name,
            o.occupationname AS occupation_name,
            concat(superv.fname, ' ', superv.lname) AS supervisor_name
            FROM personstbl p
            JOIN personoccupationstbl po ON po.perspersoccfk = p.personid
            JOIN occupationstbl o ON o.occupationid = po.occpersoccfk
            JOIN apprentsuperstbl apsup ON apsup.persappsupfk = p.personid
            JOIN supervisorstbl s ON apsup.supappsupfk = s.supervisorid
            JOIN personstbl superv ON superv.personid = s.perssupfk
            WHERE p.personid = $id
            LIMIT 1
            ";

$rs = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($rs);

// $supname = $suprow["SuperName"];
$app_name = $row['apprentice_name'];
$app_occupation = $row['occupation_name'];
$sup_name = $row['supervisor_name'];

// Get process information
// Get the apprentice's month and year of processes and the total amount of each
// SELECT DISTINCT
// owp.processletter,
// wp.pname,
// IFNULL(SUM(aop.hours), 0) as Montlysum
// FROM workprocessestbl wp
// JOIN occupationworkprocessestbl owp ON owp.procfk = wp.workprocessid
// JOIN personoccupationstbl po ON po.occpersoccfk = owp.occfk
// LEFT JOIN apprenticeoccupationprogresstbl aop ON aop.poaopfk = po.poid
// 									AND aop.owpfk = owp.owpid
// 									AND YEAR(aop.date) = 2021
//                                     AND MONTHNAME(aop.date) = 'February'
// WHERE po.perspersoccfk = 1
// GROUP BY owp.processletter
$proc_query = "SELECT DISTINCT
                owp.processletter,
                wp.pname,
                IFNULL(SUM(aop.hours), 0) as MonthlySum
                FROM workprocessestbl wp
                JOIN occupationworkprocessestbl owp ON owp.procfk = wp.workprocessid
                JOIN personoccupationstbl po ON po.occpersoccfk = owp.occfk
                LEFT JOIN apprenticeoccupationprogresstbl aop ON aop.poaopfk = po.poid
                                                    AND aop.owpfk = owp.owpid
                                                    AND YEAR(aop.date) = $year
                                                    AND MONTHNAME(aop.date) = '$month'
                WHERE po.perspersoccfk = $id
                GROUP BY owp.processletter
            ";
$proc_rs = mysqli_query($con, $proc_query);
$proc_data_array   = array();
while ($sql_data = mysqli_fetch_assoc($proc_rs)) {
    $proc_data_array[] = $sql_data;
}
$proc_data_len = count($proc_data_array);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Month</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <main id="sm_main">
        <form action="process_submit_month.php" method="post">
            <div class="sm_grid_container">
                <div class="header1">
                    <p class="underline"><?php echo $app_name ?></p>
                    <p class="underline"><?php echo $month ?></p>
                </div>
                <div class="header2">
                    <p class="underline"><?php echo $app_occupation ?></p>
                    <p class="underline"><?php echo $year ?></p>
                </div>

                <div class="main1">
                    <!-- submit process table -->
                    <div id="sm_process_table">
                        <!--submit table header -->
                        <div id="sm_table_header">
                            <div class="sm_table_cell shortcell">

                            </div>
                            <div class="sm_table_cell">
                                Process
                            </div>
                            <div class="sm_table_cell shortcell">
                                HRS
                            </div>
                        </div>
                        <!-- end submit process header -->

                        <!-- submit process table body -->
                        <div id="sm_table_body">
                            <?php foreach ($proc_data_array as $item) { ?>
                                <div class="sm_table_row">
                                    <div class="sm_table_cell shortcell">
                                        <?php echo $item['processletter']; ?>.
                                    </div>
                                    <div class="sm_table_cell">
                                        <?php echo $item['pname']; ?>
                                    </div>
                                    <div class="sm_table_cell shortcell underline">
                                        <?php echo $item['MonthlySum']; ?>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                        <!-- end submit process table body -->


                    </div>
                    <!-- end submit process table -->
                </div>
                <div class="main2">
                    <p><?php echo $sup_name ?></p>
                    <!-- skills table -->
                    <div id="sm_skills_table">
                        <!-- table body -->
                        <div id="sm_table_body">

                        </div>
                        <!-- end table body -->
                    </div>
                    <!-- end skills table -->
                    <input type="text">
                    <input id="submit_month_form" type="submit" button="submit">

                </div>
            </div>
            </div>
        </form>
    </main>

    <script src="js/submit_month.js"></script>
</body>

</html>