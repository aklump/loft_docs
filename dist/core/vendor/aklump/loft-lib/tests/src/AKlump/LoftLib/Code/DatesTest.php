<?php

namespace AKlump\LoftLib\Code;

use AKlump\LoftLib\Testing\PhpUnitTestCase;

class DatesTest extends PhpUnitTestCase {

  public function setUp() {
    $this->objArgs = [
      'America/Los_Angeles',
      'now',
      NULL,
      NULL,
      [],
    ];
    $this->createObj();
  }

  protected function createObj() {
    list($timezone, $now, $start, $interval, $defaultTime) = $this->objArgs;
    $this->obj = new Dates($timezone, $now, $start, $interval, $defaultTime);
  }

  /**
   * Provides data for testNormalizeDateOnVariousInputsWorksAsExpected.
   *
   * For these tests, today is '2017-09-20'
   */
  public function DataForTestNormalizeDateOnVariousInputsWorksAsExpectedProvider() {
    $tests = array();

    $tests[] = array(
      ['2017-09-20T19:00:00'],
      'wednesday',
    );


    $tests[] = array(
      ['2017-09-24T19:00:00'],
      'sunday',
    );

    $tests[] = array(
      ['2017-09-26T19:00:00'],
      'tuesday',
    );

    $tests[] = array(
      ['2017-09-21T19:00:00'],
      'thursday',
    );

    $tests[] = array(
      [
        '2017-01-20T20:00:00',
        '2017-04-20T19:00:00',
        '2017-07-20T19:00:00',
        '2017-10-20T19:00:00',
      ],
      'every jan, apr, jul and oct by the 20th',
    );

    $tests[] = array(
      ['2018-09-20T19:00:00'],
      '2018',
    );

    $tests[] = array(
      ['2017-10-01T03:46:23'],
      '2017-09-30T20:46:23',
    );

    $tests[] = array(
      ['2017-10-01T03:46:39'],
      '2017-09-30T20:46:39-0700',
    );
    $tests[] = array(
      ['2017-09-21T19:56:00'],
      'Sep. 21, 2017 at 12:56 America/Los_Angeles',
    );
    $tests[] = array(
      ['2017-09-02T12:13:00'],
      '9/2/17, 12:13Z',
    );

    $tests[] = array(
      ['2017-09-02T12:12:00'],
      '9/2/17, 12:12 UTC',
    );

    $tests[] = array(
      ['2017-09-02T19:56:00'],
      'Sep 02, 2017 at 12:56 America/Los_Angeles',
    );

    $tests[] = array(
      ['2017-12-02T20:56:00'],
      '12/2/17, 12:56 PST',
    );
    $tests[] = array(
      ['2017-10-08T19:00:00'],
      'october 8th',
    );
    $tests[] = array(
      ['2017-09-02T19:56:00'],
      '9/2/17, 12:56 America/Los_Angeles',
    );

    $tests[] = array(
      ['2017-09-02T19:56:00'],
      'Sep 02, 2017, 12:56 America/Los_Angeles',
    );


    $tests[] = array(
      ['2017-09-09T19:00:00'],
      '12pm America/Los_Angeles on Sep 9',
    );


    $tests[] = array(
      [
        '2017-09-01T19:00:00',
        '2017-09-16T19:00:00',
      ],
      'monthly on the 1st and 16th',
    );
    $tests[] = array(
      [
        '2017-01-01T20:00:00',
        '2017-01-16T20:00:00',
        '2017-02-01T20:00:00',
        '2017-02-16T20:00:00',
        '2017-09-01T19:00:00',
        '2017-09-16T19:00:00',
      ],
      'jan, feb and monthly on the 1st and 16th',
    );

    $tests[] = array(
      [
        '2017-09-01T19:00:00',
        '2017-09-16T19:00:00',
      ],
      'monthly on the 1st and 16th',
    );
    $tests[] = array(
      ['2017-09-20T19:00:00'],
      'monthly on the 20th',
    );
    $tests[] = array(
      ['2017-09-20T19:00:00'],
      'in september by the 20th',
    );
    $tests[] = array(
      ['2017-01-31T20:00:00', '2017-02-28T20:00:00', '2017-03-31T19:00:00'],
      'jan, feb and march by the eom',
    );
    $tests[] = array(
      ['2017-09-30T19:00:00'],
      'sep by the eom',
    );
    $tests[] = array(
      ['2017-09-20T19:00:00'],
      'sep by the 20th',
    );
    $tests[] = array(
      ['2017-09-30T20:46:39'],
      '2017-09-30T20:46:39+0000',
    );
    $tests[] = array(
      [
        '2017-01-03T20:00:00',
        '2017-03-03T20:00:00',
        '2017-09-03T19:00:00',
      ],
      'in january, march and september by the 3rd',
    );
    $tests[] = array(
      [
        '2017-01-20T20:00:00',
        '2017-03-20T19:00:00',
        '2017-05-20T19:00:00',
        '2017-08-20T19:00:00',
      ],
      'in january, march, may and august by the 20th',
    );


    return $tests;
  }

  /**
   * @dataProvider DataForTestNormalizeDateOnVariousInputsWorksAsExpectedProvider
   */
  public function testNormalizeDateOnVariousInputsWorksAsExpected($control, $subject) {
    $now = new \DateTime('2017-09-20', new \DateTimeZone('America/Los_Angeles'));

    $this->objArgs[0] = 'America/Los_Angeles';
    $this->objArgs[1] = $now->format(DATE_ISO8601);
    $this->createObj();

    $result = $this->obj->normalize($subject);

    $this->assertSame($control, $result);
  }

  public function testZReturnsAnObjectInUtcIgnoringProvidedTimezoneWhenDateIsAnObject() {
    $date = date_create('2017-10-23T10:40:36', new \DateTimeZone('America/Los_Angeles'));
    $this->assertSame('Mon, 23 Oct 2017 17:40:36 +0000', Dates::z($date, 'Arctic/Longyearbyen')
      ->format('r'));
  }

  public function testZReturnsAnObjectInUtcNotInherentTimezoneWhenDateIsAnObject() {
    $date = date_create('2017-10-23T10:40:36', new \DateTimeZone('America/Los_Angeles'));
    $this->assertSame('Mon, 23 Oct 2017 17:40:36 +0000', Dates::z($date)
      ->format('r'));
  }

  public function testOReturnsAnObjectInProvidedTimezoneBecauseItWasNotInherentWhenDateIsAString() {
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 -0700', Dates::o('2017-10-23T10:40:36', 'America/Los_Angeles')
      ->format('r'));
  }

  public function testOReturnsAnObjectInInherentTimezoneNotTheDefaultWhenDateIsObject() {
    $date = date_create('2017-10-23T10:40:36', new \DateTimeZone('America/Los_Angeles'));
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 -0700', Dates::o($date)
      ->format('r'));
  }

  public function testOReturnsAnObjectInInherentTimezoneNotTheProvidedWhenDateIsAnObject() {
    $date = date_create('2017-10-23T10:40:36', new \DateTimeZone('America/Los_Angeles'));
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 -0700', Dates::o($date, 'UTC')
      ->format('r'));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testNormalizeDateTimeThrows() {
    $this->obj->normalize(date_create());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testNormalizeIntThrows() {
    $this->obj->normalize(123);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testNormalizeArrayThrows() {
    $this->obj->normalize([]);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testNormalizeABogusStringThrows() {
    $this->obj->normalize('Some bogus string');
  }

  public function testGetFullMonths() {
    $months = Dates::getMonths();
    $this->assertSame('January', $months[1]);

    $months = Dates::getMonths('M');
    $this->assertSame('Aug', $months[8]);
  }

  public function testDefaultTimeToConstructorFeedsTimeToNormalize() {
    $this->objArgs[1] = '2017-08-31';
    $this->objArgs[4] = [14, 59, 32];
    $this->createObj();
    $return = $this->obj->normalize('October 23rd');
    $this->assertSame(['2017-10-23T21:59:32'], $return);
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testGetDaysOfTheWeekWithBogusDayThrows() {
    Dates::getDaysOfTheWeek('bogus');
  }

  public function testGetDaysOfTheWeekWithWednesdayAsStartWorks() {
    $this->assertSame(
      [
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'monday',
        'tuesday',
      ], Dates::getDaysOfTheWeek('wednesday'));
  }

  public function testGetDaysOfTheWeekReturnsASevenElementArray() {
    $this->assertInternalType('array', Dates::getDaysOfTheWeek());
    $this->assertCount(7, Dates::getDaysOfTheWeek());
  }

  public function testZuluWithoutArgumentIsNow() {
    $control = date_create('now', new \DateTimeZone('utc'))->format(DATE_ISO8601);
    $this->assertTrue(Dates::z()->format(DATE_ISO8601) >= $control);
  }

  public function testIsTodayWhenPassingDateTimeObject() {
    $date = date_create('2017-11-19T07:00:00Z');
    $this->createObj();
    $this->assertFalse($this->obj->isToday($date));
  }

  public function testIsTodayReturnsAsExpectedForStandardUse() {
    $this->objArgs[1] = '2017-11-19T07:00:00';
    $this->createObj();
    $this->assertFalse($this->obj->isToday('Nov 18th, 2017'));
    $this->assertTrue($this->obj->isToday('Nov 19th, 2017'));
    $this->assertFalse($this->obj->isToday('Nov 20th, 2017'));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testIsTodayInDaysThrowsWhenNormalizeReturnsMoreThanOneDateObjectForDay2() {
    $this->objArgs[1] = '2017-11-19T07:00:00';
    $this->createObj();
    $this->obj->isTodayInDays('nov 19th', 'monthly on the 1st and 2nd');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testIsTodayInDaysThrowsWhenNormalizeReturnsMoreThanOneDateObjectForDay1() {
    $this->objArgs[1] = '2017-11-19T07:00:00';
    $this->createObj();
    $this->obj->isTodayInDays('monthly on the 1st and 2nd', 'nov 19th');
  }

  public function testIsTodayInDaysReturnsAsExpectedForStandardUse() {
    $this->objArgs[1] = date('Y') . '-11-19T07:00:00';
    $this->createObj();
    $this->assertTrue($this->obj->isTodayInDays('Nov 18th', 'Nov 20th'));
    $this->assertTrue($this->obj->isTodayInDays('Nov 19th', 'Nov 19th'));
    $this->assertFalse($this->obj->isTodayInDays('Nov 20th', 'Nov 21th'));
    $this->assertFalse($this->obj->isTodayInDays('Nov 18th', 'Nov 18th'));
  }

  public function testZReturnsAnObjectInUtcNotInherentTimezoneWhenDateIsAString() {
    $this->assertSame('Mon, 23 Oct 2017 17:40:36 +0000', Dates::z('2017-10-23T10:40:36PDT')
      ->format('r'));
  }

  public function testZReturnsAnObjectInUtcConvertedFromDefaultTimezoneUtcBecauseItWasNotInherentWhenDateIsAString() {
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 +0000', Dates::z('2017-10-23T10:40:36')
      ->format('r'));
  }

  public function testZReturnsAnObjectInUtcConvertedFromProvidedTimezoneBecauseItWasNotInherentWhenDateIsAString() {
    $this->assertSame('Mon, 23 Oct 2017 08:40:36 +0000', Dates::z('2017-10-23T10:40:36', 'Arctic/Longyearbyen')
      ->format('r'));
  }

  public function testOReturnsAnObjectInInherentTimezoneNotTheDefaultWhenDateIsString() {
    $date = Dates::o('2017-10-23T10:40:36PDT');
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 -0700', $date->format('r'));
  }

  public function testOReturnsAnObjectInInherentTimezoneNotTheOneProvidedWhenDateIsString() {
    $date = Dates::o('2017-10-23T10:40:36PDT', 'UTC');
    $this->assertSame('Mon, 23 Oct 2017 10:40:36 -0700', $date->format('r'));
  }

  public function testFilterAfterExcludesThoseWithinAndAfter() {
    $this->objArgs[1] = '2017-03-31T19:00:00';
    $this->createObj();
    $dates = [];
    $dates[] = $this->obj->create('2017-02-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-03-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-04-10')->setTime(8, 30);

    $dates = $this->obj->filterAfter($dates);
    $this->assertCount(1, $dates);
    $this->assertSame('Mon, 10 Apr 2017 08:30:00 -0700', $dates[0]->format('r'));
  }

  public function testFilterBeforeExcludesThoseWithinAndAfter() {
    $this->objArgs[1] = '2017-03-31T19:00:00';
    $this->createObj();
    $dates = [];
    $dates[] = $this->obj->create('2017-02-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-03-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-04-10')->setTime(8, 30);

    $dates = $this->obj->filterBefore($dates);
    $this->assertCount(1, $dates);
    $this->assertSame('Fri, 10 Feb 2017 08:30:00 -0800', $dates[0]->format('r'));
  }

  public function testFilterExcludesThoseBeforeAndAfter() {
    $this->objArgs[1] = '2017-03-31T19:00:00';
    $this->createObj();
    $dates = [];
    $dates[] = $this->obj->create('2017-02-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-03-10')->setTime(8, 30);
    $dates[] = $this->obj->create('2017-04-10')->setTime(8, 30);

    $dates = $this->obj->filter($dates);
    $this->assertCount(1, $dates);
    $this->assertSame('Fri, 10 Mar 2017 08:30:00 -0800', $dates[0]->format('r'));
  }

  public function testZChangesTimeZoneToUTC() {
    $date = $this->obj->now();
    $this->assertNotSame('utc', $date->getTimeZone()->getName());
    $this->assertNotSame('utc', Dates::z($date)->getTimeZone()->getName());
  }

  public function testNormalizeDateMonthlyWithYearScopeProducesTwelveDates() {
    $this->objArgs[2] = $this->obj->create('2017-01-01T00:00:00');
    $this->objArgs[3] = new \DateInterval('P1Y');
    $this->createObj();
    $this->assertCount(12, $this->obj->normalize('monthly on the 1st'));
  }

  public function testNormalizeDateMonthlyWithFiveYearScopeProducesSixtyDates() {
    $this->objArgs[2] = $this->obj->create('2017-01-01T00:00:00');
    $this->objArgs[3] = new \DateInterval('P5Y');
    $this->createObj();
    $this->assertCount(60, $this->obj->normalize('monthly on the 1st'));
  }

  /**
   * Provides data for testGetMonthFromStringWorksOnVariousStrings.
   */
  public function DataForTestGetMonthFromStringWorksOnVariousStringsProvider() {
    $tests = array();
    $tests[] = array(1, 'jan');
    $tests[] = array(1, 'january');
    $tests[] = array(1, 'JANUARY');
    $tests[] = array(2, 'feb');
    $tests[] = array(2, 'february');
    $tests[] = array(2, 'FEBRUARY');
    $tests[] = array(12, 'decem');
    $tests[] = array(9, 'sept');

    return $tests;
  }

  /**
   * @dataProvider DataForTestGetMonthFromStringWorksOnVariousStringsProvider
   */
  public function testGetMonthFromStringWorksOnVariousStrings($control, $string) {
    $this->assertSame($control, Dates::getMonthFromString($string));
  }

  /**
   * Provides data for testNormalizeDateHandlesTimeZoneCorrectly.
   */
  public function DataForTestNormalizeDateHandlesTimeZoneCorrectlyProvider() {
    // normalized date, date to normalize, now, timezone name,
    $tests = array();

    $tests[] = array(
      '2017-11-05T12:00:00',
      'monthly on the 5th',
      '2017-11-10T12:00:00Z',
      'UTC',
    );

    // The verbal dates are when the locale.timezone is taken into account.
    $tests[] = array(

      // noon gets normalized to UTC
      '2017-11-05T20:00:00',

      // this sets it to noon LA timezone
      'monthly on the 5th',
      '2017-11-10T12:00:00Z',

      // This gives context to the 'monthly on...'
      'America/Los_Angeles',
    );
    $tests[] = array(
      '2017-10-05T08:59:59',
      '2017-10-05T08:59:59Z',
      '2017-11-10T12:00:00Z',
      'UTC',
    );
    $tests[] = array(
      '2017-10-05T20:00:00',
      '2017-10-05T13:00:00-0700',
      '2017-11-10T12:00:00Z',
      'UTC',
    );
    $tests[] = array(
      '2017-10-05T20:00:00',
      '2017-10-05T13:00:00-0700',
      '2017-11-10T12:00:00Z',
      'America/Los_Angeles',
    );


    return $tests;
  }

  /**
   * @dataProvider DataForTestNormalizeDateHandlesTimeZoneCorrectlyProvider
   */
  public function testNormalizeDateHandlesTimeZoneCorrectly($control, $subject, $now, $timezone) {
    $this->objArgs[0] = $timezone;
    $this->objArgs[1] = $now;
    $this->createObj();

    $this->assertSame([$control], $this->obj->normalize($subject));
  }

  public function testUtcReturnsAUtcTimezoneObject() {
    $this->assertSame('UTC', Dates::utc()->getName());
  }

  public function testSetSecondWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:33-0700');
    Dates::setSecond($date, '33');

    $this->assertSame('2017-10-23T11:11:33-0700', $date->format(DATE_ISO8601));
  }

  public function testSetMinuteWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:17-0700');
    Dates::setMinute($date, '59');

    $this->assertSame('2017-10-23T11:59:17-0700', $date->format(DATE_ISO8601));
  }

  public function testSetHourWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:17-0700');
    Dates::setHour($date, '18');

    $this->assertSame('2017-10-23T18:11:17-0700', $date->format(DATE_ISO8601));
  }


  public function testSetDayWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:17-0700');
    Dates::setDay($date, '07');

    $this->assertSame('2017-10-07T11:11:17-0700', $date->format(DATE_ISO8601));
  }


  public function testSetMonthWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:17-0700');
    Dates::setMonth($date, '5');

    $this->assertSame('2017-05-23T11:11:17-0700', $date->format(DATE_ISO8601));
  }


  public function testSetYearWorksAndKeepsOriginalOtherValues() {
    $date = date_create('2017-10-23T11:11:17-0700');
    Dates::setYear($date, '1994');

    $this->assertSame('1994-10-23T11:11:17-0700', $date->format(DATE_ISO8601));
  }

  public function testCreateMethodOverridesInheritTimezoneOfStringWithLocalTimezoneInInstance() {
    $date = $this->obj->create('2017-10-23T11:11:17UTC');
    $this->assertSame('America/Los_Angeles', $date->getTimeZone()->getName());
  }

  /**
   * Provides data for testTheLessThanTrimFlagWorksOnVariousCombos.
   */
  public function DataForTestTheLessThanTrimFlagWorksOnVariousCombosProvider() {
    $tests = array();
    $tests[] = array('2017-10-23T10:49:25-07', '2017-10-23T10:49:25PDT');
    $tests[] = array('2017-10-23T10:49', '2017-10-23T10:49:00Z');
    $tests[] = array('2017-10-23T10:49', '2017-10-23T10:49:00+0000');
    $tests[] = array('2017-10-23T11', '2017-10-23T11:00:00+0000');
    $tests[] = array('2017-10-23', '2017-10-23T00:00:00+0000');

    return $tests;
  }

  /**
   * @dataProvider DataForTestTheLessThanTrimFlagWorksOnVariousCombosProvider
   */
  public function testTheLessThanTrimFlagWorksOnVariousCombos($control, $source, $expand = NULL) {
    // See if it compresses
    $date = Dates::o($source);
    $compressed = Dates::format($date, DATES_FORMAT_ISO8601_TRIMMED);
    $this->assertSame($control, $compressed);
  }

  public function testConstantDatesFormatQuarterWorksAsExpected() {
    $this->assertSame('2017-Q4', Dates::format(date_create('2017-10-23T10:49:25PDT'), DATES_FORMAT_QUARTER));
  }

  public function testConstantDateIso8601ShortWorksAsExpected() {
    $this->assertSame('2017-10-23T10:49:25', date_create('2017-10-23T10:49:25PDT')->format(DATE_ISO8601_SHORT));
  }

  public function testGetNextQuarterReturnsCorrectDates() {
    $control = [
      'Mon, 01 Jan 2018 00:00:00 -0700',
      'Sat, 31 Mar 2018 23:59:59 -0700',
    ];
    $date = Dates::o('2017-10-23T10:40:36PDT');
    $return = $this->obj->getNextQuarter($date);
    $this->assertSame($control[0], $return[0]->format('r'));
    $this->assertSame($control[1], $return[1]->format('r'));

    $return = Dates::getNextQuarter($date);
    $this->assertSame($control[0], $return[0]->format('r'));
    $this->assertSame($control[1], $return[1]->format('r'));
  }

  public function testGetLastQuarterReturnsCorrectDates() {
    $control = [
      'Sat, 01 Jul 2017 00:00:00 -0700',
      'Sat, 30 Sep 2017 23:59:59 -0700',
    ];
    $date = Dates::o('2017-10-23T10:40:36PDT');
    $return = $this->obj->getLastQuarter($date);
    $this->assertSame($control[0], $return[0]->format('r'));
    $this->assertSame($control[1], $return[1]->format('r'));

    $return = Dates::getLastQuarter($date);
    $this->assertSame($control[0], $return[0]->format('r'));
    $this->assertSame($control[1], $return[1]->format('r'));
  }

  public function testFormatWithBackslashQReturnsQ() {
    $this->assertSame('q', $this->obj->format(Dates::o('2017-01-15'), '\q'));
  }

  public function testFormatWithJustQReturnsExpectedQuarterInteger() {
    $this->assertSame('1', $this->obj->format(Dates::o('2017-01-15'), 'q'));
    $this->assertSame('2', $this->obj->format(Dates::o('2017-04-15'), 'q'));
    $this->assertSame('3', $this->obj->format(Dates::o('2017-07-15'), 'q'));
    $this->assertSame('4', $this->obj->format(Dates::o('2017-10-15'), 'q'));
  }

  /**
   * Provides data for
   * testFormatWithQPlusOtherFormattersReturnsTheCorrectString.
   */
  public function DataForTestFormatWithQPlusOtherFormattersReturnsTheCorrectStringProvider() {
    $tests = array();
    $tests[] = array(
      'q: 4 2017',
      '2017-10-23T10:04:04-0700',
    );
    $tests[] = array(
      'q: 1 2017',
      '2017-02-23T10:04:04-0700',
    );
    $tests[] = array(
      'q: 2 2017',
      '2017-05-23T10:04:04-0700',
    );
    $tests[] = array(
      'q: 3 2017',
      '2017-08-23T10:04:04-0700',
    );

    return $tests;
  }

  /**
   * @dataProvider DataForTestFormatWithQPlusOtherFormattersReturnsTheCorrectStringProvider
   */
  public function TestFormatWithQPlusOtherFormattersReturnsTheCorrectString($control, $date) {
    // Test with a \DateTime
    $this->assertSame($control, $this->obj->format(date_create($date), '\q: q Y'));
    $this->assertSame($control, Dates::format(date_create($date), '\q: q Y'));

    // Now test by passing a string.
    $date = date_create($date);
    $this->assertSame($control, $this->obj->format($date, '\q: q Y'));
    $this->assertSame($control, Dates::format($date, '\q: q Y'));
  }

  public function testGetQuarterSetsFirstTimeTo0_0_0AndLastTimeTo23_59_59() {
    $subject = $this->obj->getQuarter(Dates::z('2017-10-23T08:15:00'));
    $subject = array_map(function ($value) {
      return $value->format('r');
    }, $subject);
    $this->assertSame([
      'Sun, 01 Oct 2017 00:00:00 +0000',
      'Sun, 31 Dec 2017 23:59:59 +0000',
    ], $subject);

    $subject = Dates::getQuarter(Dates::z('2017-10-23T08:15:00'));
    $subject = array_map(function ($value) {
      return $value->format('r');
    }, $subject);
    $this->assertSame([
      'Sun, 01 Oct 2017 00:00:00 +0000',
      'Sun, 31 Dec 2017 23:59:59 +0000',
    ], $subject);
  }

  /**
   * Provides data for testGetQuarterReturnsFirstAndLastDaysOfNowQuarter.
   */
  public function DataForTestGetQuarterReturnsFirstAndLastDaysOfNowQuarterProvider() {
    {
      $tests = array();
      $tests[] = array(['2017-01-01', '2017-03-31'], '2017-01-01');
      $tests[] = array(['2017-01-01', '2017-03-31'], '2017-02-01');
      $tests[] = array(['2017-01-01', '2017-03-31'], '2017-03-01');
      $tests[] = array(['2017-01-01', '2017-03-31'], '2017-03-15');
      $tests[] = array(['2017-01-01', '2017-03-31'], '2017-03-31');

      $tests[] = array(['2017-04-01', '2017-06-30'], '2017-04-01');
      $tests[] = array(['2017-04-01', '2017-06-30'], '2017-05-01');
      $tests[] = array(['2017-04-01', '2017-06-30'], '2017-06-01');
      $tests[] = array(['2017-04-01', '2017-06-30'], '2017-06-15');
      $tests[] = array(['2017-04-01', '2017-06-30'], '2017-06-30');

      $tests[] = array(['2017-07-01', '2017-09-30'], '2017-07-01');
      $tests[] = array(['2017-07-01', '2017-09-30'], '2017-08-01');
      $tests[] = array(['2017-07-01', '2017-09-30'], '2017-09-01');
      $tests[] = array(['2017-07-01', '2017-09-30'], '2017-09-15');
      $tests[] = array(['2017-07-01', '2017-09-30'], '2017-09-30');

      $tests[] = array(['2017-10-01', '2017-12-31'], '2017-10-01');
      $tests[] = array(['2017-10-01', '2017-12-31'], '2017-11-01');
      $tests[] = array(['2017-10-01', '2017-12-31'], '2017-12-01');
      $tests[] = array(['2017-10-01', '2017-12-31'], '2017-12-15');
      $tests[] = array(['2017-10-01', '2017-12-31'], '2017-12-30');

      return $tests;
    }

    $tests = array();
    $tests[] = array();

    return $tests;
  }

  /**
   * @dataProvider DataForTestGetQuarterReturnsFirstAndLastDaysOfNowQuarterProvider
   */
  public function testGetQuarterReturnsFirstAndLastDaysOfNowQuarter($control, $date) {
    $subject = $this->obj->getQuarter(date_create($date));
    $subject = array_map(function ($value) {
      return $value->format('Y-m-d');
    }, $subject);
    $this->assertSame($control, $subject);

    $subject = Dates::getQuarter(date_create($date));
    $subject = array_map(function ($value) {
      return $value->format('Y-m-d');
    }, $subject);
    $this->assertSame($control, $subject);
  }

  /**
   * Provides data for testGetQuarter.
   */
  public function DataForTestGetQuarterProvider() {
    $tests = array();
    $tests[] = array('2017-Q1', '2017-01-01');
    $tests[] = array('2017-Q1', '2017-02-01');
    $tests[] = array('2017-Q1', '2017-03-01');
    $tests[] = array('2017-Q2', '2017-04-01');
    $tests[] = array('2017-Q2', '2017-05-01');
    $tests[] = array('2017-Q2', '2017-06-01');
    $tests[] = array('2017-Q3', '2017-07-01');
    $tests[] = array('2017-Q3', '2017-08-01');
    $tests[] = array('2017-Q3', '2017-09-01');
    $tests[] = array('2017-Q4', '2017-10-01');
    $tests[] = array('2017-Q4', '2017-11-01');
    $tests[] = array('2017-Q4', '2017-12-01');

    return $tests;
  }

  /**
   * @dataProvider DataForTestGetQuarterProvider
   */
  public function testGetQuarterReturnsCorrectQuarterForEachMonthOfTheYear($control, $date) {
    $this->assertSame($control, Dates::format(Dates::o($date), DATES_FORMAT_QUARTER));
    $this->assertSame($control, Dates::format(date_create($date), DATES_FORMAT_QUARTER));
  }


}
