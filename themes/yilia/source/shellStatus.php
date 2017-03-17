
<?php
    /**
    * state
    *
    * @package custom
    */
?>
    <article class="content">
    <section class="post">
        <div id='server_state'>
            <h5>uname -a</h5>
            <pre><?php echo shell_exec("uname -a"); ?></pre><br/>
     
            <h5>Uptime</h5>
            <pre><?php echo ltrim(shell_exec("uptime"), " "); ?></pre><br/>
     
            <h5>cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq </h5>
            <pre>
                <?php
                    $t = shell_exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq");
            echo sprintf("%s/1000=%sKHz", str_replace("\n", "", $t), $t/1000);
                ?>
            </pre><br/>
     
            <h5>cat /sys/class/thermal/thermal_zone0/temp </h5>
            <pre>
                <?php
                    $t = shell_exec("cat /sys/class/thermal/thermal_zone0/temp");
        echo sprintf("%s/1000=%sC°", str_replace("\n", "", $t), $t/1000);
       ?>
      </pre><br/>
     
            <h5>free -h</h5>
            <pre><?php echo shell_exec("free -h"); ?></pre><br/>
     
            <h5>cat /proc/cpuinfo</h5>
            <pre><?php echo shell_exec("cat /proc/cpuinfo"); ?></pre>
</div>

