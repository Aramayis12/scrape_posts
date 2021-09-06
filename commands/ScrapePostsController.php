<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use app\components\SimpleHtmlDom;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ScrapePostsController extends Controller
{
	private $scrap_url = 'https://10web.io/blog/';
	private $words_count = [];
	private $scraped_count = 0;
	private $start_date = null;
	private $end_date = null;
	private $limit = null;

	# There is also a corpus of stopwords, that is, high-frequency words like the, to and also that we sometimes want to filter out of a document before further processing 
	private $stopwords = [
		'i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours',
		'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers',
		'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves',
		'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are',
		'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does',
		'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until',
		'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into',
		'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down',
		'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here',
		'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more',
		'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so',
		'than', 'too', 'very', 's', 't', 'can', 'will', 'just', 'don', 'should', 'now',
		'v','li'
	];


    /**
     * This command scrape posts from blog page.
     * @param integer $limit articles limit.
     * @param string $range date range.
     * @return int Exit code
     */
    public function actionIndex($limit = null, $range=null)
	{
		// get range
		if(!is_numeric($limit) || intval($limit) < 0){
			$this->stderr('Error: ' . ExitCode::getReason(ExitCode::USAGE) . PHP_EOL);
			return ExitCode::USAGE;
		}

		$this->limit = intval($limit);

		// get dates
		if(!is_null($range)){
			$date_explode = explode('/', $range);

			if(count($date_explode) != 2){
				$this->stderr('Error: ' . ExitCode::getReason(ExitCode::USAGE) . PHP_EOL);
				return ExitCode::USAGE;
			}

			$start_date = $date_explode[0];
			$end_date = $date_explode[1];

			// check if options are date format
			if(!strtotime($start_date) || !strtotime($end_date)){
				$this->stderr('Error: ' . ExitCode::getReason(ExitCode::USAGE) . PHP_EOL);
				return ExitCode::USAGE;
			}

			// check if end date is bigger then start date
			if(strtotime($end_date) < strtotime($start_date)){
				$this->stderr('Error: ' . ExitCode::getReason(ExitCode::USAGE) . PHP_EOL);
				return ExitCode::USAGE;
			}

			$this->start_date = strtotime($start_date);
			$this->end_date = strtotime($end_date);
		}

		// Remove old data
		$this->removeOldData();

		$data = [];

		// Get blogs page url
		$url = $this->scrap_url;

		$this->stdout('Parsing' . PHP_EOL, Console::FG_YELLOW);

		// scrape posts
		$this->scrapePostsFromUrl($url);

		// words count logic
		$words_count = $this->words_count;

		if(count($words_count) > 0){
			foreach($words_count as $date => $day_words) {
				$max_word = ['count' => 0, 'title' => ''];

				// Count most used word
				foreach($day_words as $word => $count){
					if($count > $max_word['count']){
						$max_word['count'] = $count;
						$max_word['title'] = $word;
					}			
				}

				if($max_word['title'] != ''){
					// Insert in table
					Yii::$app->db->createCommand()->insert('top_words', [
						'title' => $max_word['title'],
						'count' => $max_word['count'],
						'date' => $date,
					])->execute();
				}
			}
		}


        return ExitCode::OK;
    }

	private function removeOldData(){
		// DELETE (top_words, posts)
		Yii::$app->db->createCommand()->truncateTable('top_words')->execute();
		Yii::$app->db->createCommand()->truncateTable('posts')->execute();

		// Remove old post images
		FileHelper::removeDirectory(Yii::$app->basePath . '/web/uploads/');
	}

	private function scrapePostsFromUrl($url){
		$stop = false;

        // Create DOM from URL
        $simple_html_dom = new SimpleHtmlDom();
        $html = $simple_html_dom->file_get_html($url);

		// Start Latest Posts Loop
        foreach ($html->find('.blog-post') as $post) {
			$data = [];

            // Get post url
            $url = $post->find('.blog-post-title > a', 0)->attr['href'];

			// get these data from URL page: title, author, featured image, excerpt, scraped date, article date
			$post_html = $simple_html_dom->file_get_html($url);
			$content = $post_html->find('.post_content', 0);
			
			$data['article_date'] = strtotime($content->find('time', 0)->attr['datetime']);

			// if range start date is bigger then post date break the loop, no sense to continue check old posts
			if($this->start_date > $data['article_date']){
				$stop = true;
				break;
			}

			// check if post date is out of range
			if($data['article_date'] < $this->start_date || $data['article_date'] > $this->end_date){
				continue;
			}

			$data['title'] = $content->find('.entry-title', 0)->plaintext;
			$data['author'] = $content->find('.author', 0)->plaintext;
			$data['scraped_date'] = time();
			$data['excerpt'] = trim($post->find('.entry-summary', 0)->plaintext);

			# Download Image
			// getting all sizes of images
			$images = $content->find('.entry-thumbnail img', 0)->attr['src'];
			$images = explode(',', $images);

			// getting small size image from all images
			$image = trim(end($images));
			$image = explode(' ', $image);
			$image = $image[0];

			// upload image
			$data['featured_image'] = $this->uploadBlogFeaturedImage($image);

			// print on cli
			$this->stdout(date('Y-m-d', $data['article_date']) . ' ' . $data['title'] .  PHP_EOL, Console::FG_GREEN);

			// count words
			$post_content = $content->find('.entry-content', 0)->plaintext;
			$this->countMostUsedWords(strtolower($post_content), $data['article_date']);

			// Insert in table
			$data['article_date'] = date("Y-m-d H:i:s", $data['article_date']);
			$data['scraped_date'] = date("Y-m-d H:i:s", $data['scraped_date']);

			Yii::$app->db->createCommand()->insert('posts', $data)->execute();

			//
			$this->scraped_count++;

			if($this->scraped_count == $this->limit){
				$stop = true;
				break;
			}
        }

		// open next page posts
		if(!$stop){
			$next_page = $html->find('.page-navigation .current', 0)->next_sibling();

			if(!is_null($next_page)){
				$url = $next_page->attr['href'];

				$this->scrapePostsFromUrl($url);
			}
		}
	}

	private function uploadBlogFeaturedImage($image){
		// upload image
		$image_content = file_get_contents($image);

		// Get Image New Path
		$image_name = Yii::$app->getSecurity()->generateRandomString() . '.jpg';
		$path = Yii::getAlias('@app/web/uploads/posts/');
		FileHelper::createDirectory($path);
		$image_upload_path = $path . 'featured_image_' . $image_name;

		// Upload Image
		$upload = file_put_contents($image_upload_path, $image_content);

		$image_upload_path = ($upload)?str_replace('/app/web/', '', $image_upload_path):null;

		return $image_upload_path;
	}

	private function countMostUsedWords($searchString, $timestamp) {
		$wordsFromSearchString = str_word_count($searchString, 1);
		$finalWords = array_diff($wordsFromSearchString, $this->stopwords);

		$result = $this->words_count;

		$date_index = date('Y-m-d', $timestamp);

		foreach ($finalWords as $word) {
			if(!isset($result[$date_index])){
				$result[$date_index] = [];
			}

			if(!isset($result[$date_index][$word])){
				$result[$date_index][$word] = 0;
			}

			$result[$date_index][$word]++;
		}

		$this->words_count = $result;
	}
}
