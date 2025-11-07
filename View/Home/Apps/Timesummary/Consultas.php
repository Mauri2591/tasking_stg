<?php
require_once __DIR__."/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if(isset($_SESSION['usu_id'])){
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";
    Headers::get_cors();
    include_once __DIR__ . "/../../Public/Template/head.php";
    include_once __DIR__ . "/../../Public/Template/main_content.php";


?>
    <div class="page-content">
        <div class="container-fluid">

        <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Consultar</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>        

        <table>
            <tr>
                <th>uno</th>
                <th>dos</th>
                <th>tres</th>
            </tr>
        </table>

        </div>
    </div>
<?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
}
?>
<script></script>
     