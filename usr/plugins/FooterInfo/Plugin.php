<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 自定义页脚文字插件 
 * 
 * @package FooterInfo 
 * @author zizhuoye.chen
 * @version 1.0.0
 * @link http://www.czzy.me/blog/
 */
class FooterInfo_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
       Typecho_Plugin::factory('Widget_Archive')->footer->___showAdditionalFooter = array('FooterInfo_Plugin', 'render');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
?>
		<div style="color:#999;font-size:0.92857em;font-weight:bold;"><p><?php _e('在footer.php的页脚位置插入代码%s即可. ','<span style="color:#467B96;">&lt;?php $this->showAdditionalFooter(); ?&gt;</span>'); ?><br/>
<?php
        /** 分类名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('word', NULL, '', _t('页脚信息'));
        $form->addInput($name);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
		echo '<p>'
			. htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('FooterInfo')->word)
			.'</p>';
    }
}
