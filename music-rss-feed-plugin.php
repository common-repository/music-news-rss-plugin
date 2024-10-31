<?php
/*
Plugin Name: Music Rss Feed
Plugin URI: http://www.guyro.com/music-news-rss-plugin
Description: Adds a customizeable widget which displays the latest Music news from The Music Blog. 
Version: 1.0
Author: Guy Roman
Author URI: http://www.guyro.com
License: GPL3
*/

function musicnews()
{
  $options = get_option("widget_musicnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Music News',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Feed 
  $rss = simplexml_load_file( 
  'http://feeds2.feedburner.com/MuchMusicBlog'); 
  ?> 
  
  <ul> 
  
  <?php 
  // max number of news slots, with 0 (zero) all display
  $max_news = $options['news'];
  // maximum length to which a title may be reduced if necessary,
  $max_length = $options['chars'];
  
  // RSS Elements 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Title
    $title = $i->title;
    // Length of title
    $length = strlen($title);
    // if the title is longer than the previously defined maximum length,
    // it'll he shortened and "..." added, or it'll output normaly
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_musicnews($args)
{
  extract($args);
  
  $options = get_option("widget_musicnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Music News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  musicnews();
  echo $after_widget;
}

function musicnews_control()
{
  $options = get_option("widget_musicnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Music News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['musicnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['musicnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['musicnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['musicnews-CharCount']);
    update_option("widget_musicnews", $options);
  }
?> 
  <p>
    <label for="musicnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="musicnews-WidgetTitle" name="musicnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="musicnews-NewsCount">Max. News: </label>
    <input type="text" id="musicnews-NewsCount" name="musicnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="musicnews-CharCount">Max. Characters: </label>
    <input type="text" id="musicnews-CharCount" name="musicnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="musicnews-Submit"  name="musicnews-Submit" value="1" />
  </p>
  
<?php
}

function musicnews_init()
{
  register_sidebar_widget(__('Music News'), 'widget_musicnews');    
  register_widget_control('Music News', 'musicnews_control', 300, 200);
}
add_action("plugins_loaded", "musicnews_init");
?>