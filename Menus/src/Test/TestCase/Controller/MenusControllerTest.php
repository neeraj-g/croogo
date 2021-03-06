<?php
namespace Croogo\Menus\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use Menus\Controller\MenusController;

class MenusControllerTest extends CroogoControllerTestCase
{

    public $fixtures = [
        'plugin.users.aco',
        'plugin.users.aro',
        'plugin.users.aros_aco',
        'plugin.blocks.block',
        'plugin.comments.comment',
        'plugin.contacts.contact',
        'plugin.translate.i18n',
        'plugin.settings.language',
        'plugin.contacts.message',
        'plugin.nodes.node',
        'plugin.taxonomy.model_taxonomy',
        'plugin.blocks.region',
        'plugin.users.role',
        'plugin.settings.setting',
        'plugin.menus.menu',
        'plugin.menus.link',
        'plugin.meta.meta',
        'plugin.taxonomy.taxonomy',
        'plugin.taxonomy.term',
        'plugin.taxonomy.type',
        'plugin.taxonomy.types_vocabulary',
        'plugin.users.user',
        'plugin.taxonomy.vocabulary',
    ];

/**
 * setUp
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
        $this->MenusController = $this->generate('Menus.Menus', [
            'methods' => [
                'redirect',
            ],
            'components' => [
                'Auth' => ['user'],
                'Session',
            ],
        ]);
        $this->MenusController->Auth
            ->staticExpects($this->any())
            ->method('user')
            ->will($this->returnCallback([$this, 'authUserCallback']));
    }

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->MenusController);
    }

/**
 * testAdminIndex
 *
 * @return void
 */
    public function testAdminIndex()
    {
        $this->testAction('/admin/menus/menus/index');
        $this->assertNotEmpty($this->vars['menus']);
    }

/**
 * testAdminAdd
 *
 * @return void
 */
    public function testAdminAdd()
    {
        $this->expectFlashAndRedirect('The Menu has been saved');
        $mainMenu = ClassRegistry::init('Menus.Menu')->findByAlias('main');
        $this->testAction('/admin/menus/menus/add', [
            'data' => [
                'Menu' => [
                    'title' => 'New Menu',
                    'description' => 'A new menu',
                    'alias' => 'new',
                    'link_count' => 0,
                ],
            ],
        ]);
        $newMenu = $this->MenusController->Menu->findByAlias('new');
        $this->assertEqual($newMenu['Menu']['title'], 'New Menu');
    }

/**
 * testAdminEdit
 *
 * @return void
 */
    public function testAdminEdit()
    {
        $this->expectFlashAndRedirect('The Menu has been saved');
        $this->testAction('/admin/menus/menus/edit/1', [
            'data' => [
                'Menu' => [
                    'id' => 3, // main
                    'title' => 'Main Menu [modified]',
                ],
            ],
        ]);
        $result = $this->MenusController->Menu->findByAlias('main');
        $this->assertEquals('Main Menu [modified]', $result['Menu']['title']);
    }

/**
 * testAdminDelete
 *
 * @return void
 */
    public function testAdminDelete()
    {
        $this->expectFlashAndRedirect('Menu deleted');
        $this->testAction('/admin/menus/menus/delete/4');
        $hasAny = $this->MenusController->Menu->hasAny([
            'Menu.alias' => 'footer',
        ]);
        $this->assertFalse($hasAny);
    }
}
