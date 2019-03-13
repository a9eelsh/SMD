<?php
//new SMD beta v2
//Coder By @ia9a9sh
$url = $_GET["url"];
switch (true) {
    case strpos($url, 'twitter') > 0:
      $url = explode('status/',$url);
      $request = curl_init();
      $bearer = "AAAAAAAAAAAAAAAAAAAAABXsygAAAAAAmJuTUyoBiQiFqw9KIVOZPoELi%2FM%3DPau8s2HiUxM9v3eiOtJQu3bdKcCbaHyw5le0yH1LLhfJh6580X";
      curl_setopt($request, CURLOPT_SSL_VERIFYPEER, TRUE);
      curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($request, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/show.json?tweet_mode=extended&id='.$url[1].'');
      curl_setopt($request, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$bearer));
      curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 2);
      $response = curl_exec($request);
      $result = json_decode($response, true);
      foreach($result["extended_entities"]['media'] as $media){
        $aa =$media['video_info']['variants'];
        foreach($aa  as $vid){
          if(strpos($vid['url'], "mp4") !== false){
            preg_match('/[0-9]+[0-9]x[0-9]+/m', $vid['url'], $matches);
            echo $matches[0]." ".$vid['url']."<br>";
          }
        }
      }
    break;
    case strpos($url, 'youtu') > 0 :
       $url = str_replace("youtu.be/","www.youtube.com/watch?v=",$url);

        $rx = '~
        ^(?:https?://)?
          (?:www\.)?
          (?:m\.)?
          (?:youtube\.com|youtu\.be)
          /watch\?v=([^&]+)
          ~x';
          if (preg_match($rx, $url)){
          $url = explode('?', $url)[1];
          $url = explode('=', $url);
          $code = $url[array_search('v', $url)+1];
          }


       $video_info = file_get_contents("https://www.youtube.com/get_video_info?video_id=".$code."&asv=2");
       parse_str($video_info);
       $my_formats_array = explode(',' , $adaptive_fmts);
       $videos = explode(',',$url_encoded_fmt_stream_map);
       foreach ($videos as $video){
         parse_str($video,$video_array);
         switch ($video_array['itag']) {
           case "17":
             $resolution = "176x144";
           break;
           case "18":
             $resolution =  "480x360";
           break;
           case "22":
             $resolution =  "1280x720";
           break;
           case "36":
             $resolution =  "320x240";
           break;
           case "37":
             $resolution =  "1920x1080";
           break;
           case "38":
             $resolution =  "2048x1080";
           break;
         }
          $type=explode(";",$video_array['type']);
          if($type[0] =="video/mp4" || $type[0] =="video/3gpp"){
         	echo $resolution." ".$video_array['url']."<br>";
         }
      }
          $i =0;
       foreach ($my_formats_array as $format) {
         parse_str($format);
        //parse_str($format,$video_array);
          // echo $video_array['itag'].'aaa';
         if($itag == "140"){
         	echo "audio ".urldecode($url)."<br>";
         }
           $i++;
     }
     break;
     case strpos($url, 'facebook') > 0:
        $url = str_replace("m.facebook.com","www.facebook.com",$url);
        $context = ['http' => ['method' => 'GET','header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36",],];
        $context = stream_context_create($context);
        $data = file_get_contents($url, false, $context);
            if (preg_match('/sd_src_no_ratelimit:"([^"]+)"/', $data, $match1)) {
              echo "360 ".$match1[1]."<br>";
            }
            if (preg_match('/hd_src_no_ratelimit:"([^"]+)"/', $data, $match)) {
              echo "720 ".$match[1]."<br>";
            }
            break;
      case strpos($url, 'instagram') > 0:
            $insta_source = file_get_contents($url); // instagrame tag url
            $shards = explode('window._sharedData = ', $insta_source);
            $insta_json = explode(';</script>', $shards[1]);
            $data = json_decode($insta_json[0], TRUE);

            if ($data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['__typename'] == "GraphImage") {
                  $imagesdata         = $data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_resources'];
                  $length             = count($imagesdata);
                  echo "123 ".$imagesdata[$length - 1]['src']."<br>";
            } else {
            if ($data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['__typename'] == "GraphSidecar") {
                  $counter      = 0;
                  $multipledata = $data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'];
                  foreach ($multipledata as &$media) {
                        if ($media['node']['is_video'] == "true") {
                              echo $counter." ".$media['node']['video_url']."<br>";
                        } else {
                              $length = count($media['node']['display_resources']);
                              echo $counter." ".$media['node']['display_resources'][$length - 1]['src']."<br>";
                        }
                        $counter++;
                  }
            } else {
                  if ($data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['__typename'] == "GraphVideo") {
                        echo "123 ".$data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['video_url']."<br>";
                  }

            }
          }
      break;
}
?>
