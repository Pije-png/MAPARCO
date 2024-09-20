<style>
        .nav-container {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            top: 0;
            left: 0;
            right: 0;
            background-color: green;
            /* background-color: slategray; */
        }

        .nav-profile {
            margin-left: auto;
        }

        .nav-profile img {
            width: 30px;
            border-radius: 50%;
            border: 1px solid #fff;
        }
    </style>
    <section class="nav-home">
        <div class="nav-container">
            <div class="nav-profile">

                <p><img src="MAPARCO.png" alt="profile"></p>
            </div>
        </div>
    </section>
<script>
    $(document).ready(function(){
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>