<?php

/**
 * Class that contains all simple CRUD operations for Blogger.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SimpleCRUD
{
    /**
     * $blogID - Blog ID used for demo operations
     *
     * @var string
     */
    public $blogID;

    /**
     * $gdClient - Client class used to communicate with the Blogger service
     *
     * @var Zend_Gdata_Client
     */
    public $gdClient;


    /**
     * Constructor for the class. Takes in user credentials and generates the
     * the authenticated client object.
     *
     * @param  string $email    The user's email address.
     * @param  string $password The user's password.
     * @return void
     */
    public function __construct($email, $password)
    {
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, 'blogger');
        $this->gdClient = new Zend_Gdata($client);
    }

    /**
     * This function retrieves all the blogs associated with the authenticated
     * user and prompts the user to choose which to manipulate.
     *
     * Once the index is selected by the user, the corresponding blogID is
     * extracted and stored for easy access.
     *
     * @return void
     */
    public function promptForBlogID()
    {
        print "Your blogs in bloggers.com. Type number to show their blogIDs.\n";
        
        $query = new Zend_Gdata_Query('http://www.blogger.com/feeds/default/blogs');
        $feed = $this->gdClient->getFeed($query);
        $this->printFeed($feed);
        $input = getInput("\nNumber:");

        //id text is of the form: tag:blogger.com,1999:user-blogID.blogs
        $idText = explode('-', $feed->entries[$input]->id->text);
        print "Your blogID is:\n{$idText[2]}\n\nWrite this value in setting.yaml.\n";
        $this->blogID = $idText[2];
    }

    /**
     * This function creates a new Zend_Gdata_Entry representing a blog
     * post, and inserts it into the user's blog. It also checks for
     * whether the post should be added as a draft or as a published
     * post.
     *
     * @param  string  $title   The title of the blog post.
     * @param  string  $content The body of the post.
     * @param  boolean $isDraft Whether the post should be added as a draft or as a published post
     * @return string The newly created post's ID
     */
    public function createPost($arg)
    {
        // We're using the magic factory method to create a Zend_Gdata_Entry.
        // http://framework.zend.com/manual/en/zend.gdata.html#zend.gdata.introdduction.magicfactory
        $entry = $this->gdClient->newEntry();

        $entry->title = $this->gdClient->newTitle($arg['entrytitle']);
        $entry->content = $this->gdClient->newContent($arg['entrycontent']);
        $entry->content->setType($arg['contenttype']);
        $uri = "http://www.blogger.com/feeds/" . $this->blogID . "/posts/default";

        if ($arg['is_draft'])
        {
            $control = $this->gdClient->newControl();
            $draft = $this->gdClient->newDraft('yes');
            $control->setDraft($draft);
            $entry->control = $control;
        }

        $createdPost = $this->gdClient->insertEntry($entry, $uri);
        //format of id text: tag:blogger.com,1999:blog-blogID.post-postID
        $idText = explode('-', $createdPost->id->text);
        $postID = $idText[2];

        return $postID;
    }


    /**
     * Retrieves the specified post and updates the title and body. Also sets
     * the post's draft status.
     *
     * @param string  $postID         The ID of the post to update. PostID in <id> field:
     *                                tag:blogger.com,1999:blog-blogID.post-postID
     * @param string  $updatedTitle   The new title of the post.
     * @param string  $updatedContent The new body of the post.
     * @param boolean $isDraft        Whether the post will be published or saved as a draft.
     * @return Zend_Gdata_Entry The updated post.
     */
    public function updatePost($arg)
    {
	    	$postID=$arg['post_id'];
	    	$isDraft=$arg['is_draft'];
        $query = new Zend_Gdata_Query(
        	'http://www.blogger.com/feeds/' . $this->blogID . '/posts/default/' . $postID
        );
        $postToUpdate = $this->gdClient->getEntry($query);
//        $postToUpdate->title->text = $this->gdClient->newTitle(trim($arg['entrytitle']));
//        $postToUpdate->content->text = $this->gdClient->newContent(trim($arg['entrycontent']));
				$postToUpdate->published->text = $this->gdClient->newContent(trim($arg['date_published']));
				$postToUpdate->updated->text = $this->gdClient->newContent(trim($arg['date_updated']));

print_r($postToUpdate->published->text);

        if ($isDraft) {
            $draft = $this->gdClient->newDraft('yes');
        } else {
            $draft = $this->gdClient->newDraft('no');
        }

        $control = $this->gdClient->newControl();
        $control->setDraft($draft);
        $postToUpdate->control = $control;
        $updatedPost = $postToUpdate->save();

        return $updatedPost;
    }

    /**
     * Helper function to print out the titles of all supplied Blogger
     * feeds.
     *
     * @param  Zend_Gdata_Feed The feed to print.
     * @return void
     */
    public function printFeed($feed)
    {
        $i = 0;
        foreach($feed->entries as $entry)
        {
            echo "\t" . $i ." ". $entry->title->text . "\n";
            $i++;
        }
    }


}

