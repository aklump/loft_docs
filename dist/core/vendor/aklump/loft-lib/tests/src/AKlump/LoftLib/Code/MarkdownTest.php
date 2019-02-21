<?php

namespace AKlump\LoftLib\Code;


use AKlump\LoftLib\Testing\PhpUnitTestCase;

class MarkdownTest extends PhpUnitTestCase {

    public function testRemoveLastItemFromInnerList()
    {
        $this->objArgs[0] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.\n\n## Relates To\n\n* [TC-254: fpo wip](https://trello.com/c/gFuH4jQM/71-tc-254-fpo-wip)
* [TC-236: add an icon, which when clicked, copies the value of the current running timer.](https://trello.com/c/53IoRrkt/61-tc-236-add-an-icon-which-when-clicked-copies-the-value-of-the-current-running-timer)\n\n## Is Depended On By\n\n* [TC-254: fpo wip](https://trello.com/c/gFuH4jQM/71-tc-254-fpo-wip)";
        $this->createObj();
        $control = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ac blandit risus. Mauris tempor a lacus a placerat. Vivamus viverra dapibus metus non finibus. Nulla ultricies est nulla, eget efficitur nibh viverra non. Sed sed est viverra nunc malesuada venenatis vitae at tellus. Suspendisse potenti. Morbi non blandit elit, sit amet consectetur mi.\n\n## Relates To\n\n* [TC-254: fpo wip](https://trello.com/c/gFuH4jQM/71-tc-254-fpo-wip)\n\n## Is Depended On By\n\n* [TC-254: fpo wip](https://trello.com/c/gFuH4jQM/71-tc-254-fpo-wip)";
        $this->assertSame($control, $this->obj
            ->removeItemFromList('Relates To', '[TC-236: add an icon, which when clicked, copies the value of the current running timer.](https://trello.com/c/53IoRrkt/61-tc-236-add-an-icon-which-when-clicked-copies-the-value-of-the-current-running-timer)')->getMarkdown());
    }

    public function testRemoveItemFromInnerList()
    {
        $this->objArgs[0] = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n* [TC-244: FPO](https://trello.com/c/vZsWmHme/63-tc-244-fpo)\n\n## Depends On\n\n* TC-254: FPO";
        $this->createObj();
        $control = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n## Depends On\n\n* TC-254: FPO";
        $this->assertSame($control, $this->obj
            ->removeItemFromList('Relates To', '[TC-244: FPO](https://trello.com/c/vZsWmHme/63-tc-244-fpo)')->getMarkdown());
    }

    public function testRemoveItemFromList()
    {
        $this->objArgs[0] = "## Depends On\n\n* TC-244: FPO";
        $this->createObj();
        $control = "## Depends On";
        $this->assertSame($control, $this->obj
            ->removeItemFromList('Depends On', 'TC-244: FPO')->getMarkdown());
    }

    public function testRemoveItemFromNonExistentList()
    {
        $this->objArgs[0] = "lorem ipsum";
        $this->createObj();
        $control = "lorem ipsum";
        $this->assertSame($control, $this->obj
            ->removeItemFromList('Depends On', 'TC-244: FPO')->getMarkdown());
    }

    public function testAddToBottomOfAnotherListEnsuringSpacing()
    {
        $this->objArgs[0] = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n## Depends On\n\n* Me";
        $this->createObj();
        $control = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n* You\n\n## Depends On\n\n* Me";
        $this->assertSame($control, $this->obj
            ->addItemToList('Relates To', 'You')->getMarkdown());
    }

    public function testAddToBottomOfAnotherList()
    {
        $this->objArgs[0] = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n* [TC-244: FPO](https://trello.com/c/vZsWmHme/63-tc-244-fpo)";
        $this->createObj();
        $control = "Praesent at vulputate tellus, vehicula dignissim elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In molestie est sapien, sit amet porttitor ipsum laoreet sit amet. Praesent lacus neque, suscipit quis urna non, varius consequat justo. Curabitur tincidunt leo ac venenatis pharetra. Curabitur ultricies odio ut nibh viverra lobortis. Sed scelerisque pellentesque vehicula.\n\n## Relates To\n\n* [TC-244: FPO](https://trello.com/c/vZsWmHme/63-tc-244-fpo)\n\n## Depends On\n\n* TC-254: FPO";
        $this->assertSame($control, $this->obj
            ->addItemToList('Depends On', 'TC-254: FPO')->getMarkdown());
    }

    public function testAddItemToSingleParagraph()
    {
        $this->objArgs[0] = "Here is something";
        $this->createObj();
        $control = "Here is something\n\n## Depends On\n\n* TC-254: FPO";
        $this->assertSame($control, $this->obj
            ->addItemToList('Depends On', 'TC-254: FPO')->getMarkdown());
    }

    public function testAddItemToExistingList()
    {
        $this->objArgs[0] = "Here is something\n\n## Depends On\n* TC-244: FPO";
        $this->createObj();
        $control = "Here is something\n\n## Depends On\n\n* TC-254: FPO\n* TC-244: FPO";
        $this->assertSame($control, $this->obj
            ->addItemToList('Depends On', 'TC-254: FPO')->getMarkdown());
    }

    public function testAddItemToContentWithoutList()
    {
        $this->objArgs[0] = "Here is something\n\n";
        $this->createObj();
        $control = "Here is something\n\n## Depends On\n\n* TC-254: FPO";
        $this->assertSame($control, $this->obj
            ->addItemToList('Depends On', 'TC-254: FPO')->getMarkdown());
    }

    public function testAddItemToNonExistentList()
    {
        $control = "## Depends On\n\n* TC-244: FPO";
        $this->assertSame($control, $this->obj
            ->addItemToList('Depends On', 'TC-244: FPO')->getMarkdown());
    }

    public function testValueWithPipeGetsEscaped()
    {
        $subject = [
            ['value' => 'aaron | brian | justin'],
        ];
        $control = '| value |
|---|
| aaron \| brian \| justin |
';
        $this->assertSame($control, Markdown::table($subject));
    }

    public function testArrayValuesConvertsToJson()
    {
        $list = ['c', 'd', 'e'];
        $subject = [
            ['list' => $list],
        ];
        $control = '| list |
|---|
| ["c","d","e"] |
';
        $this->assertSame($control, Markdown::table($subject));
    }

    public function testThreeRowWithSeparateKeysReturnsExpectedTable()
    {
        $subject = [
            ['name' => 'Aaron', 'age' => 43],
            ['name' => 'Brian', 'age' => 38],
            ['name' => 'Justin', 'age' => 34],
        ];
        $control = '| a | b |
|---|---|
| Aaron | 43 |
| Brian | 38 |
| Justin | 34 |
';

        $this->assertSame($control, Markdown::table($subject, ['a', 'b']));
    }

    public function testOneRowReturnsExpectedTable()
    {
        $subject = [
            ['name' => 'Aaron', 'age' => 43],
        ];
        $control = '| name | age |
|---|---|
| Aaron | 43 |
';

        $this->assertSame($control, Markdown::table($subject));
    }

    public function setUp()
    {
        $this->objArgs = [''];
        $this->createObj();
    }

    protected function createObj()
    {
        $this->obj = new Markdown($this->objArgs[0]);
    }
}
