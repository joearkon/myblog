<?php
/**
 * 蜘蛛来访日志查看
 * 
 * @package RobotsPlus
 * @author  YoviSun
 * @version 2.0.0
 * @update: 2013.5.02
 * @link http://www.yovisun.com
 */
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
		<?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <div class="typecho-list-operate">
                    <?php
						$config  = Typecho_Widget::widget('Widget_Options')->plugin('RobotsPlus');
						$botlist = $config->botlist;
						$pagecount = $config->pagecount;
						$isdrop = $config->droptable;
						if ($botlist == null || $pagecount == null || $isdrop == null)
						{
							throw new Typecho_Plugin_Exception('请先设置插件！');
						}
						$db = Typecho_Db::get();
						$prefix = $db->getPrefix();
						$p = 1;
						$rtype = '';
						$oldtype = '';
						if (isset($_POST['rpage'])) {
							$p = $_POST['rpage'];
						}
						if (isset($_POST['do'])) {
							$do = $_POST['do'];
							if ($do == 'delete') {
								if (isset($_POST['lid'])) {
									$lids = $_POST['lid'];
									$deleteCount = 0;
									if ($lids && is_array($lids)) {
										foreach ($lids as $lid) {
											if ($db->query($db->delete($prefix.'robots_logs')->where('lid = ?', $lid))) {
												$deleteCount ++;
											}
										}
									}
									Typecho_Widget::widget('Widget_Notice')->set('成功删除蜘蛛日志',NULL,'success');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=RobotsPlus%2FLogs.php', $options->adminUrl));
								}else{
									Typecho_Widget::widget('Widget_Notice')->set('当前没有选择的日志',NULL,'notice');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=RobotsPlus%2FLogs.php', $options->adminUrl));
								}
							}
							if (strpos($do,'clear')!==false)
							{
								try
								{
									$cleartype = substr($do, 6);
									$options = Typecho_Widget::widget('Widget_Options');
									$timeStamp = $options->gmtTime;
									$offset = $options->timezone - $options->serverTimezone;
									$gtime = $timeStamp + $offset;
									$lowtime = $gtime - ($cleartype * 86400);
									$db->query($db->delete($prefix.'robots_logs')->where('ltime < ?', $lowtime));
									Typecho_Widget::widget('Widget_Notice')->set('清除日志成功',NULL,'success');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=RobotsPlus%2FLogs.php', $options->adminUrl));
								}
								catch (Typecho_Db_Exception $e)
								{
									Typecho_Widget::widget('Widget_Notice')->set('清除日志失败',NULL,'notice');
									Typecho_Response::redirect(Typecho_Common::url('extending.php?panel=RobotsPlus%2FLogs.php', $options->adminUrl));
								}
							}
						}
						if (isset($_POST['oldtype'])) {
							$oldtype = $_POST['oldtype'];
						}
						if (isset($_POST['rpage']) && $_POST['rtype']!=='') {
							$rtype = $_POST['rtype'];
							if ($oldtype !== $rtype) {
								$p = 1;
							}
							$logs = $db->fetchAll($db->select()->from($prefix.'robots_logs')->where('bot = ?', $rtype)->order($prefix.'robots_logs.lid', Typecho_Db::SORT_DESC)->page($p, $pagecount));
							$rows = count($db->fetchAll($db->select('lid')->from($prefix.'robots_logs')->where('bot = ?', $rtype)));
						}else{
							$logs = $db->fetchAll($db->select()->from($prefix.'robots_logs')->order($prefix.'robots_logs.lid', Typecho_Db::SORT_DESC)->page($p, $pagecount));
							$rows = count($db->fetchAll($db->select('lid')->from($prefix.'robots_logs')));
						}
						$co = $rows % $pagecount;
						$pageno = floor($rows / $pagecount);
						if ($co !== 0) {
							$pageno += 1;
						}
                    ?>
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=RobotsPlus%2FLogs.php'); ?>">
                    <p class="operate">操作: 
                        <span class="operate-button typecho-table-select-all">全选</span>, 
                        <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                        选中项: 
                        <span rel="delete" lang="你确认要删除这些日志吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span>&nbsp;&nbsp;&nbsp;
						清除选项:
						<span rel="clear_0" lang="你确认要清除所有的日志吗?" class="operate-button operate-clear typecho-table-select-submit">清除所有</span>
						<span rel="clear_1" lang="你确认要只保留一天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一天</span>
						<span rel="clear_2" lang="你确认要只保留两天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留两天</span>
						<span rel="clear_3" lang="你确认要只保留三天内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留三天</span>
						<span rel="clear_7" lang="你确认要只保留一周内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一周</span>
						<span rel="clear_15" lang="你确认要只保留半个月内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留半个月</span>
						<span rel="clear_30" lang="你确认要只保留一个月内的日志吗?" class="operate-button operate-clear typecho-table-select-submit">保留一个月</span>
                    </p>
                    <p class="search">
                    <select name="rpage">
                        <?php for ($i = 1; $i <= $pageno; $i++): ?>
                    	<option value="<?php echo $i ?>" <?php if ($i == $p): ?>selected="selected"<?php endif; ?>>第<?php echo $i ?>页</option>
                        <?php endfor; ?>
                    </select>
                    <select name="rtype">
                    	<option value="">所有蜘蛛</option>
                        						<?php
												if (sizeof($botlist)>0) {
													foreach ($botlist as $bot) {
														$selected = $rtype==$bot ? ' selected="selected"' : NULL;
														echo '<option value="'.$bot.'"'.$selected.'>'.botname($bot).'</option>';
													}
												}
												function botname($bot)
												{
													switch ($bot) {
														case "baidu":
															return '百度';
															break;
														case "google":
															return '谷歌';
															break;
														case "yahoo":
															return '雅虎';
															break;
														case "sogou":
															return '搜狗';
															break;
														case "youdao":
															return '有道';
															break;
														case "soso":
															return '搜搜';
															break;
														case "bing":
															return '必应';
															break;
                                                                                                                case "360":
															return '360搜索';
															break;
													}
												}
												?>
                    </select>
                    <button type="submit">查看</button>
                                        </p>
                <input type="hidden" name="do" value="select" />
                <input type="hidden" name="oldtype" value="<?php echo $rtype; ?>" />
                </form>
                </div>
            
                <form method="post" action="<?php $options->adminUrl('extending.php?panel=RobotsPlus%2FLogs.php'); ?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="260"/>
                        <col width="60"/>
                        <col width="30"/>
                        <col width="110"/>
                        <col width="205"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th>受访地址</th>
                            <th> </th>
                            <th> </th>
                            <th>蜘蛛名称</th>
                            <th>IP地址<a style="padding-left:12px;" href="javascript:void(0);" onclick="showIpLocation();">查询位置</a></th>
                            <th class="typecho-radius-topright">日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
						<tr class="even" id="post-5">
							<td><input type="checkbox" value="<?php echo $log['lid']; ?>" name="lid[]"/></td>
                            <td></td>
                            <td colspan="2"><a href="<?php echo str_replace("%23", "#", $log['url']); ?>"><?php echo urldecode(str_replace("%23", "#", $log['url'])); ?></a></td>
                            <td></td>
                            <td><?php echo botname($log['bot']); ?></td>
                            <td><div class="robotx_ip"><?php echo $log['ip']; ?></div><div class="robotx_location"></div></td>
                            <td><?php echo date('Y-m-d H:i:s',$log['ltime']); ?></td>
                        </tr>
					<?php endforeach; ?>
                    <?php else: ?>
                    <tr class="even">
                        <td colspan="8"><h6 class="typecho-list-table-title"><?php _e('当前无蜘蛛日志'); ?></h6></td>
                    </tr>
                    <?php endif; ?>
					</tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.4.1/jquery.min.js"></script>
<script type="text/javascript">
/*解决jquery库Mootools库之间的冲突*/
jQuery.noConflict();//释放jquery中$定义，并直接使用jQuery代替平时的$
function showIpLocation(){	
		jQuery(".robotx_location").text("正在查询...");		
		jQuery(".robotx_ip").each(function(){
			var myd = jQuery(this);
		  jQuery.getScript("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=" + myd.text(),function(){ 
		  	var ipadd = "没有找到匹配的 IP 地址信息";
			  if (remote_ip_info.ret == '1'){
			 			ipadd = remote_ip_info.country + " " 
					  + remote_ip_info.province + " " 
					  + remote_ip_info.district + " " 
					  + remote_ip_info.desc + " " 
					  + remote_ip_info.isp;
					  myd.next().text(ipadd).css("color","#BD6800");
				}else{
					myd.next().text(ipadd).css("color","#f00");
				}				
			});
	});
}
</script>

<?php
include 'copyright.php';
include 'common-js.php';
?>
<?php include 'footer.php'; ?>