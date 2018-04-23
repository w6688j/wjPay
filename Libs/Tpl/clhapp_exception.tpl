<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
　<head>
    　　<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    　　<title><?php echo $mailTitle ?></title>
    　　<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    　</head>
<body style='background-color: #e5e5e5;'>
<table border='0' cellpadding='0' cellspacing='0' width='100%'
       style='font-size: 13px; color: #333; line-height: 20px; font-family: "Helvetica Neue",Helvetica,"Microsoft Yahei","Hiragino Sans GB","WenQuanYi Micro Hei",Tahoma,Arial,sans-serif;'>
    <tbody>
    <tr>
        <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                   style="border: none;border-collapse: collapse;">
                <tbody>
                <tr>
                    <td style="padding: 10.0px 0;border: none;vertical-align: middle;">
                        <strong style="font-size: 16.0px;">系统错误报告</strong></td>
                </tr>
                </tbody>
            </table>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                   style="border-collapse: collapse;background-color: rgb(255,255,255);border: 1.0px solid rgb(207,207,207);margin-bottom: 20.0px;font-size: 13.0px;">
                <tbody>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" width="600"
                               style="border: none;border-collapse: collapse;">
                            <tbody>
                            <tr>
                                <td style="padding: 10.0px;background-color: rgb(248,250,254);border: none;font-size: 14.0px;font-weight: 500;border-bottom: 1.0px solid rgb(229,229,229);">
                                    <a href="javascript:;"
                                       style="color: rgb(51,51,51);" target="_blank"><?=$e['title']?></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;border: none;">
                        <fieldset style="border: 1.0px solid rgb(229,229,229);">
                            <legend style="color: rgb(17,79,142);">主要错误</legend>
                            <div style="padding: 5.0px;">
                                <p>
                                    <span style="color: grey;">SQL错误:</span>
                                    <?=$e['sql']?>
                                </p>
                                <p>
                                    <span style="color: red;">PHP错误:</span>
                                    <?=$e['error']?>
                                </p>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;border: none;">
                        <fieldset style="border: 1.0px solid rgb(229,229,229);">
                            <legend style="color: rgb(17,79,142);">代码步骤</legend>
                            <div style="padding: 5.0px;">
                                <?php foreach($e['traces'] as $t){ ?>
                                <p style="word-break: break-all">
                                    <span style="color: black"><?=$t[1]?></span>
                                    <span style="color: red"><?=$t[2]?></span>
                                    <br>
                                    <span style="color: grey">[<?=$t[5]?>]</span>
                                    by
                                    <span style="color: green"><?=$t[6]?></span>
                                </p>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;border: none;">
                        <fieldset style="border: 1.0px solid rgb(229,229,229);">
                            <legend style="color: rgb(17,79,142);">GET</legend>
                            <div style="padding: 5.0px;">
                                <?php foreach($e['get'] as $k=>$v){ ?>
                                <p>
                                    <span
                                            style="color: grey;width: 150px;text-align: left;display: inline-block"><?=$k?></span>
                                    =>
                                    <span style="color:blue;"><?=$v?></span>
                                </p>
                                <?php } ?>
                                <?php if(empty($_GET)){ ?>
                                <p>无post参数</p>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;border: none;">
                        <fieldset style="border: 1.0px solid rgb(229,229,229);">
                            <legend style="color: rgb(17,79,142);">POST</legend>
                            <div style="padding: 5.0px;">
                                <?php foreach($e['post'] as $k=>$v){ ?>
                                <p>
                                    <span
                                            style="color: grey;width: 150px;text-align: left;display: inline-block"><?=$k?></span>
                                    =>
                                    <span style="color:blue;"><?=$v?></span>
                                </p>
                                <?php } ?>
                                <?php if(empty($_POST)){ ?>
                                <p>无post参数</p>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;border: none;">
                        <fieldset style="border: 1.0px solid rgb(229,229,229);">
                            <legend style="color: rgb(17,79,142);">SERVER</legend>
                            <div style="padding: 5.0px;">
                                <?php foreach($e['server'] as $k=>$v){ ?>
                                <p>
                                    <span
                                            style="color: grey;width: 150px;text-align: left;display: inline-block"><?=$k?></span>
                                    =>
                                    <span style="color:blue;"><?=$v?></span>
                                </p>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10.0px;background-color: rgb(255,240,213);">
                        <span style="font-size: 16.0px;color: rgb(241,163,37);">●</span>
                        &nbsp;<span>
                        <a class="calNotifyLink" date="1476320400000" href="javascript:;"
                           style="color:#fe6600;border-bottom: dashed 1px #999;text-decoration: none;cursor: pointer;">
                            <?=date('Y-m-d H:i:s')?>
                        </a>&nbsp;潜在负责人&nbsp;<strong><?=$e['user']?></strong> </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body></html>