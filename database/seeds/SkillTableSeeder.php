<?php

use Illuminate\Database\Seeder;
use App\Skill;

class SkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
    		['name' => 'Laravel Framework'],
    		['name' => 'PHP'],
    		['name' => 'Magento'],
    		['name' => 'Node JS'],
    		['name' => 'Angular JS'],
    		['name' => 'Wordpress'],
    		['name' => 'Javascript'],
    		['name' => 'HTML'],
    		['name' => 'CakePHP Framework'],
    		['name' => 'AJAX'],
    		['name' => 'Phpfox'],
    		['name' => 'CSS'],
    		['name' => 'MySQL'],
    		['name' => 'CodeIgniter'],
    		['name' => 'Joomla'],
    		['name' => 'MongoDB'],
    		['name' => 'Cassandra'],
    		['name' => 'Zend'],
    		['name' => 'React JS'],
    		['name' => 'Meteor JS'],
            ['name' => 'Symphony'],
            ['name' => 'Yii'],
            ['name' => 'Mean JS'],
            ['name' => 'Selenium'],
            ['name' => 'REST API'],
            ['name' => 'Java'],
            ['name' => 'ASP.NET'],
            ['name' => 'HTML5'],
            ['name' => 'Python'],
            ['name' => 'Django'],
            ['name' => '.NET'],
            ['name' => 'ASP'],
            ['name' => 'SQL Server'],
            ['name' => 'Ruby'],
            ['name' => 'RoR'],
            ['name' => 'Interspire'],
            ['name' => 'Bigcommerce'],
            ['name' => 'Opencart'],
            ['name' => 'Lead Generation'],
            ['name' => 'Adobe Fireworks'],
            ['name' => 'Selenium Webdriver'],
            ['name' => 'QTP'],
            ['name' => 'Silk'],
            ['name' => 'UFT'],
            ['name' => 'Rational Robot'],
            ['name' => 'Opensta'],
            ['name' => 'Jmeter'],
            ['name' => 'Katalon'],
            ['name' => 'Jasmine'],
            ['name' => 'Gulp'],
            ['name' => 'Sahi'],
            ['name' => 'Waitr'],
            ['name' => 'Testcomplete'],
            ['name' => 'SOAP API'],
            ['name' => 'RPC'],
            ['name' => 'Adobe Photoshop'],
            ['name' => 'Adobe Dreamweaver'],
            ['name' => 'Adobe Illustrator'],
            ['name' => 'Slim'],
            ['name' => 'Boonex Dolphin'],
            ['name' => 'SocialEngine'],
            ['name' => 'Sprout'],
            ['name' => 'Zoho'],
            ['name' => 'SalesForce'],
            ['name' => 'Apex API'],
            ['name' => 'JomSocial'],
            ['name' => 'Elgg'],
            ['name' => 'CodeSniffer'],
            ['name' => 'PHPmd'],
            ['name' => 'NoSQL'],
            ['name' => 'MySQL Workbench'],
            ['name' => 'Teamwork PM Tool'],
            ['name' => 'Redmine'],
            ['name' => 'Phantom JS']
    	];

    	foreach ($skills as $key => $value) {
        	Skill::create($value);
        }
    }
}
