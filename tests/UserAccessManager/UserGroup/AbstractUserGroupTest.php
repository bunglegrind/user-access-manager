<?php
/**
 * AbstractUserGroupTest.php
 *
 * The AbstractUserGroupTest unit test class file.
 *
 * PHP versions 5
 *
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/
 */
namespace UserAccessManager\UserGroup;

use PHPUnit_Extensions_Constraint_StringMatchIgnoreWhitespace as MatchIgnoreWhitespace;
use UserAccessManager\Config\MainConfig;
use UserAccessManager\Database\Database;
use UserAccessManager\ObjectHandler\ObjectHandler;
use UserAccessManager\UserAccessManagerTestCase;
use UserAccessManager\Util\Util;
use UserAccessManager\Wrapper\Php;
use UserAccessManager\Wrapper\Wordpress;

/**
 * Class AbstractUserGroupTest
 *
 * @package UserAccessManager\UserGroup
 */
class AbstractUserGroupTest extends UserAccessManagerTestCase
{
    /**
     * @param Php                          $php
     * @param Wordpress                    $wordpress
     * @param Database                     $database
     * @param MainConfig                   $config
     * @param Util                         $util
     * @param ObjectHandler                $objectHandler
     * @param AssignmentInformationFactory $assignmentInformationFactory
     * @param null                         $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractUserGroup
     */
    private function getStub(
        Php $php,
        Wordpress $wordpress,
        Database $database,
        MainConfig $config,
        Util $util,
        ObjectHandler $objectHandler,
        AssignmentInformationFactory $assignmentInformationFactory,
        $id = null
    ) {
        $stub = $this->getMockForAbstractClass(
            '\UserAccessManager\UserGroup\AbstractUserGroup',
            [],
            '',
            false
        );

        self::setValue($stub, 'php', $php);
        self::setValue($stub, 'wordpress', $wordpress);
        self::setValue($stub, 'database', $database);
        self::setValue($stub, 'config', $config);
        self::setValue($stub, 'util', $util);
        self::setValue($stub, 'objectHandler', $objectHandler);
        self::setValue($stub, 'assignmentInformationFactory', $assignmentInformationFactory);
        self::setValue($stub, 'id', $id);

        return $stub;
    }

    /**
     * @param string $type
     * @param string $fromDate
     * @param string $toDate
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\UserAccessManager\UserGroup\AssignmentInformation
     */
    private function getAssignmentInformation($type, $fromDate = null, $toDate = null)
    {
        $assignmentInformation = $this->createMock('\UserAccessManager\UserGroup\AssignmentInformation');

        $assignmentInformation->expects($this->any())
            ->method('getType')
            ->willReturn($type);

        $assignmentInformation->expects($this->any())
            ->method('getFromDate')
            ->willReturn($fromDate);

        $assignmentInformation->expects($this->any())
            ->method('getToDate')
            ->willReturn($toDate);

        return $assignmentInformation;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AssignmentInformationFactory
     */
    protected function getAssignmentInformationFactory()
    {
        $assignmentInformationFactory = parent::getAssignmentInformationFactory();
        $assignmentInformationFactory->expects($this->any())
            ->method('createAssignmentInformation')
            ->will($this->returnCallback(function ($type, $fromDate = null, $toDate = null) {
                return $this->getAssignmentInformation($type, $fromDate, $toDate);
            }));

        return $assignmentInformationFactory;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::__construct()
     */
    public function testCanCreateInstance()
    {
        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $this->getDatabase(),
            $this->getMainConfig(),
            $this->getUtil(),
            $this->getObjectHandler(),
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'type', 'type');
        $abstractUserGroup->__construct(
            $this->getPhp(),
            $this->getWordpress(),
            $this->getDatabase(),
            $this->getMainConfig(),
            $this->getUtil(),
            $this->getObjectHandler(),
            $this->getAssignmentInformationFactory()
        );

        self::assertInstanceOf(AbstractUserGroup::class, $abstractUserGroup);
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::__construct()
     */
    public function testUserGroupTypeException()
    {
        self::expectException(UserGroupTypeException::class);
        $this->getMockForAbstractClass(
            '\UserAccessManager\UserGroup\AbstractUserGroup',
            [
                $this->getPhp(),
                $this->getWordpress(),
                $this->getDatabase(),
                $this->getMainConfig(),
                $this->getUtil(),
                $this->getObjectHandler(),
                $this->getAssignmentInformationFactory()
            ]
        );
    }

    /**
     * @group   unit
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getId()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getType()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getName()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getDescription()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getReadAccess()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getWriteAccess()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getIpRange()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getIpRangeArray()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::setName()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::setDescription()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::setReadAccess()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::setWriteAccess()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::setIpRange()
     */
    public function testSimpleGetterSetter()
    {
        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $this->getDatabase(),
            $this->getMainConfig(),
            $this->getUtil(),
            $this->getObjectHandler(),
            $this->getAssignmentInformationFactory(),
            2
        );

        self::setValue($abstractUserGroup, 'type', 'type');
        self::setValue($abstractUserGroup, 'name', 'groupName');
        self::setValue($abstractUserGroup, 'description', 'groupDesc');
        self::setValue($abstractUserGroup, 'readAccess', 'readAccess');
        self::setValue($abstractUserGroup, 'writeAccess', 'writeAccess');
        self::setValue($abstractUserGroup, 'ipRange', 'ipRange;ipRange2');

        self::assertEquals(2, $abstractUserGroup->getId());
        self::assertEquals('type', $abstractUserGroup->getType());
        self::assertEquals('groupName', $abstractUserGroup->getName());
        self::assertEquals('groupDesc', $abstractUserGroup->getDescription());
        self::assertEquals('readAccess', $abstractUserGroup->getReadAccess());
        self::assertEquals('writeAccess', $abstractUserGroup->getWriteAccess());
        self::assertEquals(['ipRange', 'ipRange2'], $abstractUserGroup->getIpRangeArray());
        self::assertEquals('ipRange;ipRange2', $abstractUserGroup->getIpRange());

        $abstractUserGroup->setName('groupNameNew');
        self::assertAttributeEquals('groupNameNew', 'name', $abstractUserGroup);

        $abstractUserGroup->setDescription('groupDescNew');
        self::assertAttributeEquals('groupDescNew', 'description', $abstractUserGroup);

        $abstractUserGroup->setReadAccess('readAccessNew');
        self::assertAttributeEquals('readAccessNew', 'readAccess', $abstractUserGroup);

        $abstractUserGroup->setWriteAccess('writeAccessNew');
        self::assertAttributeEquals('writeAccessNew', 'writeAccess', $abstractUserGroup);

        $abstractUserGroup->setIpRange(['ipRangeNew', 'ipRangeNew2']);
        self::assertAttributeEquals('ipRangeNew;ipRangeNew2', 'ipRange', $abstractUserGroup);
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::addObject()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::resetObjects()
     */
    public function testAddObject()
    {
        $database = $this->getDatabase();

        $database->expects($this->exactly(2))
            ->method('getUserGroupToObjectTable')
            ->will($this->returnValue('userGroupToObjectTable'));

        $database->expects($this->exactly(2))
            ->method('insert')
            ->withConsecutive(
                [
                    'userGroupToObjectTable',
                    [
                        'group_id' => 123,
                        'group_type' => 'type',
                        'object_id' => 321,
                        'general_object_type' => 'generalObjectType',
                        'object_type' => 'objectType',
                        'from_date' => null,
                        'to_date' => null
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
                ],
                [
                    'userGroupToObjectTable',
                    [
                        'group_id' => 123,
                        'group_type' => 'type',
                        'object_id' => 321,
                        'general_object_type' => 'generalObjectType',
                        'object_type' => 'objectType',
                        'from_date' => 'fromDate',
                        'to_date' => 'toDate'
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
                ]
            )
            ->will($this->onConsecutiveCalls(false, true));

        $objectHandler = $this->getObjectHandler();

        $objectHandler->expects($this->exactly(5))
            ->method('getGeneralObjectType')
            ->withConsecutive(
                ['invalid'],
                ['generalObjectType'],
                ['notValidObjectType'],
                ['objectType'],
                ['objectType']
            )
            ->will($this->onConsecutiveCalls(
                null,
                null,
                'generalNotValidObjectType',
                'generalObjectType',
                'generalObjectType'
            ));

        $objectHandler->expects($this->exactly(3))
            ->method('isValidObjectType')
            ->withConsecutive(['notValidObjectType'], ['objectType'], ['objectType'])
            ->will($this->onConsecutiveCalls(false, true, true));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $this->getMainConfig(),
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);
        self::setValue($abstractUserGroup, 'type', 'type');
        self::setValue($abstractUserGroup, 'assignedObjects', [1 => 'post', 2 => 'post']);
        self::setValue($abstractUserGroup, 'roleMembership', [1 => 'role', 2 => 'role']);
        self::setValue($abstractUserGroup, 'userMembership', [1 => 'user', 2 => 'user']);
        self::setValue($abstractUserGroup, 'termMembership', [1 => 'term', 2 => 'term']);
        self::setValue($abstractUserGroup, 'postMembership', [1 => 'post', 2 => 'post']);
        self::setValue($abstractUserGroup, 'fullObjectMembership', [1 => 'post', 2 => 'post']);

        self::assertFalse($abstractUserGroup->addObject('invalid', 321));
        self::assertFalse($abstractUserGroup->addObject('generalObjectType', 321));
        self::assertFalse($abstractUserGroup->addObject('notValidObjectType', 321));
        self::assertFalse($abstractUserGroup->addObject('objectType', 321));
        self::assertTrue($abstractUserGroup->addObject('objectType', 321, 'fromDate', 'toDate'));

        self::assertAttributeEquals([], 'assignedObjects', $abstractUserGroup);
        self::assertAttributeEquals([], 'roleMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'userMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'termMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'postMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'fullObjectMembership', $abstractUserGroup);
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::delete()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::removeObject()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::resetObjects()
     */
    public function testDelete()
    {
        $database = $this->getDatabase();

        $database->expects($this->exactly(4))
            ->method('getUserGroupToObjectTable')
            ->will($this->returnValue('userGroupToObjectTable'));

        $database->expects($this->exactly(4))
            ->method('prepare')
            ->withConsecutive(
                [
                    new MatchIgnoreWhitespace(
                        'DELETE FROM userGroupToObjectTable
                        WHERE group_id = %d
                          AND group_type = \'%s\'
                          AND (general_object_type = \'%s\' OR object_type = \'%s\')'
                    ),
                    [123, 'type', 'objectType', 'objectType']
                ],
                [
                    new MatchIgnoreWhitespace(
                        'DELETE FROM userGroupToObjectTable
                        WHERE group_id = %d
                          AND group_type = \'%s\'
                          AND (general_object_type = \'%s\' OR object_type = \'%s\')'
                    ),
                    [123, 'type', 'objectType', 'objectType']
                ],
                [
                    new MatchIgnoreWhitespace(
                        'DELETE FROM userGroupToObjectTable
                            WHERE group_id = %d
                              AND group_type = \'%s\'
                              AND (general_object_type = \'%s\' OR object_type = \'%s\')'
                    ),
                    [123, 'type', 'objectType', 'objectType']
                ],
                [
                    new MatchIgnoreWhitespace(
                        'DELETE FROM userGroupToObjectTable
                            WHERE group_id = %d
                              AND group_type = \'%s\'
                              AND (general_object_type = \'%s\' OR object_type = \'%s\')
                              AND object_id = %d'
                    ),
                    [123, 'type', 'objectType', 'objectType', 1]
                ]
            )
            ->will($this->returnValue('preparedQuery'));

        $database->expects($this->exactly(4))
            ->method('query')
            ->with('preparedQuery')
            ->will($this->onConsecutiveCalls(true, false, true, true));

        $objectHandler = $this->getObjectHandler();

        $objectHandler->expects($this->once())
            ->method('getAllObjectTypes')
            ->will($this->returnValue(['objectType']));

        $objectHandler->expects($this->exactly(6))
            ->method('getGeneralObjectType')
            ->withConsecutive(
                ['objectType'],
                ['invalid'],
                ['invalidObjectType'],
                ['objectType'],
                ['objectType'],
                ['objectType'],
                ['objectType']
            )
            ->will($this->returnCallback(function ($type) {
                return ($type !== 'invalid') ? $type : null;
            }));

        $objectHandler->expects($this->exactly(5))
            ->method('isValidObjectType')
            ->withConsecutive(
                ['objectType'],
                ['invalidObjectType'],
                ['objectType'],
                ['objectType'],
                ['objectType']
            )
            ->will($this->returnCallback(function ($type) {
                return ($type === 'objectType');
            }));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $this->getMainConfig(),
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory(),
            123
        );

        self::setValue($abstractUserGroup, 'type', 'type');
        self::setValue($abstractUserGroup, 'assignedObjects', [1 => 1]);
        self::setValue($abstractUserGroup, 'roleMembership', [2 => 2]);
        self::setValue($abstractUserGroup, 'userMembership', [3 => 3]);
        self::setValue($abstractUserGroup, 'termMembership', [4 => 4]);
        self::setValue($abstractUserGroup, 'postMembership', [5 => 5]);
        self::setValue($abstractUserGroup, 'fullObjectMembership', [6 => 6]);

        self::assertTrue($abstractUserGroup->delete());

        self::assertAttributeEquals([], 'assignedObjects', $abstractUserGroup);
        self::assertAttributeEquals([], 'roleMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'userMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'termMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'postMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'fullObjectMembership', $abstractUserGroup);

        self::setValue($abstractUserGroup, 'assignedObjects', [1 => 1]);
        self::setValue($abstractUserGroup, 'roleMembership', [2 => 2]);
        self::setValue($abstractUserGroup, 'userMembership', [3 => 3]);
        self::setValue($abstractUserGroup, 'termMembership', [4 => 4]);
        self::setValue($abstractUserGroup, 'postMembership', [5 => 5]);
        self::setValue($abstractUserGroup, 'fullObjectMembership', [6 => 6]);

        self::assertFalse($abstractUserGroup->removeObject('invalid'));
        self::assertFalse($abstractUserGroup->removeObject('invalidObjectType'));

        self::assertAttributeEquals([1 => 1], 'assignedObjects', $abstractUserGroup);
        self::assertAttributeEquals([2 => 2], 'roleMembership', $abstractUserGroup);
        self::assertAttributeEquals([3 => 3], 'userMembership', $abstractUserGroup);
        self::assertAttributeEquals([4 => 4], 'termMembership', $abstractUserGroup);
        self::assertAttributeEquals([5 => 5], 'postMembership', $abstractUserGroup);
        self::assertAttributeEquals([6 => 6], 'fullObjectMembership', $abstractUserGroup);

        self::assertFalse($abstractUserGroup->removeObject('objectType'));

        self::assertAttributeEquals([1 => 1], 'assignedObjects', $abstractUserGroup);
        self::assertAttributeEquals([2 => 2], 'roleMembership', $abstractUserGroup);
        self::assertAttributeEquals([3 => 3], 'userMembership', $abstractUserGroup);
        self::assertAttributeEquals([4 => 4], 'termMembership', $abstractUserGroup);
        self::assertAttributeEquals([5 => 5], 'postMembership', $abstractUserGroup);
        self::assertAttributeEquals([6 => 6], 'fullObjectMembership', $abstractUserGroup);

        self::assertTrue($abstractUserGroup->removeObject('objectType'));

        self::assertAttributeEquals([], 'assignedObjects', $abstractUserGroup);
        self::assertAttributeEquals([], 'roleMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'userMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'termMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'postMembership', $abstractUserGroup);
        self::assertAttributeEquals([], 'fullObjectMembership', $abstractUserGroup);

        self::assertTrue($abstractUserGroup->removeObject('objectType', 1));
    }

    /**
     * Generates return values.
     *
     * @param int    $number
     * @param string $type
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    private function generateReturn($number, $type, $fromDate = null, $toDate = null)
    {
        $returns = [];

        for ($counter = 1; $counter <= $number; $counter++) {
            $return = new \stdClass();
            $return->id = $counter;
            $return->objectType = $type;
            $return->fromDate = $fromDate;
            $return->toDate = $toDate;
            $returns[] = $return;
        }

        return $returns;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getAssignedObjects()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getSimpleAssignedObjects()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::isObjectAssignedToGroup()
     */
    public function testAssignedObject()
    {
        $wordpress = $this->getWordpress();

        $wordpress->expects($this->exactly(3))
            ->method('currentTime')
            ->with('mysql')
            ->will($this->returnValue('time'));

        $database = $this->getDatabase();

        $database->expects($this->exactly(3))
            ->method('getUserGroupToObjectTable')
            ->will($this->returnValue('userGroupToObjectTable'));

        $query = 'SELECT object_id AS id, object_type AS objectType, from_date AS fromDate, to_date AS toDate
            FROM userGroupToObjectTable
            WHERE group_id = \'%s\'
              AND group_type = \'%s\'
              AND (general_object_type = \'%s\' OR object_type = \'%s\')
              AND (from_date IS NULL OR from_date >= \'%s\')
              AND (to_date IS NULL OR to_date <= \'%s\')';

        $database->expects($this->exactly(3))
            ->method('prepare')
            ->withConsecutive(
                [
                    new MatchIgnoreWhitespace($query),
                    [123, null, 'noResultObjectType', 'noResultObjectType', 'time', 'time']
                ],
                [new MatchIgnoreWhitespace($query), [123, null, 'objectType', 'objectType', 'time', 'time']],
                [new MatchIgnoreWhitespace($query), [123, null, 'something', 'something', 'time', 'time']]
            )
            ->will($this->onConsecutiveCalls(
                'nonResultPreparedQuery',
                'preparedQuery',
                'nonResultSomethingPreparedQuery'
            ));

        $database->expects($this->exactly(3))
            ->method('getResults')
            ->withConsecutive(
                ['nonResultPreparedQuery'],
                ['preparedQuery'],
                ['nonResultSomethingPreparedQuery']
            )
            ->will($this->onConsecutiveCalls(null, $this->generateReturn(3, 'objectType'), null));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $wordpress,
            $database,
            $this->getMainConfig(),
            $this->getUtil(),
            $this->getObjectHandler(),
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);

        $result = self::callMethod($abstractUserGroup, 'getAssignedObjects', ['noResultObjectType']);
        self::assertEquals([], $result);
        self::assertAttributeEquals(['noResultObjectType' => []], 'assignedObjects', $abstractUserGroup);

        $result = self::callMethod($abstractUserGroup, 'getAssignedObjects', ['objectType']);
        self::assertEquals(
            [
                1 => $this->getAssignmentInformation('objectType'),
                2 => $this->getAssignmentInformation('objectType'),
                3 => $this->getAssignmentInformation('objectType')
            ],
            $result
        );
        self::assertAttributeEquals(
            [
                'noResultObjectType' => [],
                'objectType' => [
                    1 => $this->getAssignmentInformation('objectType'),
                    2 => $this->getAssignmentInformation('objectType'),
                    3 => $this->getAssignmentInformation('objectType')
                ]
            ],
            'assignedObjects',
            $abstractUserGroup
        );

        $result = self::callMethod($abstractUserGroup, 'getAssignedObjects', ['objectType']);
        self::assertEquals(
            [
                1 => $this->getAssignmentInformation('objectType'),
                2 => $this->getAssignmentInformation('objectType'),
                3 => $this->getAssignmentInformation('objectType')
            ],
            $result
        );

        $result = self::callMethod($abstractUserGroup, 'getSimpleAssignedObjects', ['objectType']);
        self::assertEquals(
            [
                1 => 'objectType',
                2 => 'objectType',
                3 => 'objectType'
            ],
            $result
        );

        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['objectType', 1]);
        self::assertTrue($result);
        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['objectType', 2]);
        self::assertTrue($result);
        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['objectType', 3]);
        self::assertTrue($result);

        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['objectType', 4]);
        self::assertFalse($result);
        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['noResultObjectType', 1]);
        self::assertFalse($result);
        $result = self::callMethod($abstractUserGroup, 'isObjectAssignedToGroup', ['something', 1]);
        self::assertFalse($result);
    }

    /**
     * Returns the database mock for the member tests
     *
     * @param array  $types
     * @param array  $getResultsWith
     * @param array  $getResultsWill
     * @param string $fromDate
     * @param string $toDate
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\UserAccessManager\Database\Database
     */
    private function getDatabaseMockForMemberTests(
        array $types,
        array $getResultsWith = [],
        array $getResultsWill = [],
        $fromDate = null,
        $toDate = null
    ) {
        $query = 'SELECT object_id AS id, object_type AS objectType, from_date AS fromDate, to_date AS toDate
            FROM userGroupToObjectTable
            WHERE group_id = \'%s\'
              AND group_type = \'%s\'
              AND (general_object_type = \'%s\' OR object_type = \'%s\')
              AND (from_date IS NULL OR from_date >= \'%s\')
              AND (to_date IS NULL OR to_date <= \'%s\')';

        $prepareWith = [];
        $prepareWill = [];

        foreach ($types as $type => $numberOfReturn) {
            $prepareWith[] = [new MatchIgnoreWhitespace($query), [123, null, "_{$type}_", "_{$type}_", null, null]];
            $prepareWill[] = "{$type}PreparedQuery";
            $getResultsWith[] = ["{$type}PreparedQuery"];
            $getResultsWill[] = $this->generateReturn($numberOfReturn, $type, $fromDate, $toDate);
        }

        $database = $this->getDatabase();

        $database->expects($this->any())
            ->method('getUserGroupToObjectTable')
            ->will($this->returnValue('userGroupToObjectTable'));

        $database->expects($this->exactly(count($prepareWith)))
            ->method('prepare')
            ->withConsecutive(...$prepareWith)
            ->will($this->onConsecutiveCalls(...$prepareWill));

        $database->expects($this->exactly(count($getResultsWith)))
            ->method('getResults')
            ->withConsecutive(...$getResultsWith)
            ->will($this->onConsecutiveCalls(...$getResultsWill));

        return $database;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isObjectRecursiveMember()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isRoleMember()
     *
     * @return AbstractUserGroup
     */
    public function testIsRoleMember()
    {
        $database = $this->getDatabaseMockForMemberTests(['role' => 3]);

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $this->getMainConfig(),
            $this->getUtil(),
            $this->getObjectHandler(),
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);
        $recursiveMembership = [];

        $return = $abstractUserGroup->isRoleMember(1, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isRoleMember(4, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        return $abstractUserGroup;
    }

    /**
     * Prototype function for the testIsUserMember
     *
     * @param array  $types
     * @param array  $getResultsWith
     * @param array  $getResultsWill
     * @param array  $arrayFillWith
     * @param int    $expectGetUsersTable
     * @param int    $expectGetCapabilitiesTable
     * @param int    $expectGetUser
     * @param string $fromDate
     * @param string $toDate
     *
     * @return AbstractUserGroup
     */
    private function getTestIsUserMemberPrototype(
        array $types,
        array $getResultsWith,
        array $getResultsWill,
        array $arrayFillWith,
        $expectGetUsersTable,
        $expectGetCapabilitiesTable,
        $expectGetUser,
        $fromDate = null,
        $toDate = null
    ) {
        $php = $this->getPhp();

        $php->expects($this->exactly(count($arrayFillWith)))
            ->method('arrayFill')
            ->will($this->returnCallback(function ($startIndex, $numberOfElements, $value) {
                return array_fill($startIndex, $numberOfElements, $value);
            }));

        $database = $this->getDatabaseMockForMemberTests(
            $types,
            $getResultsWith,
            $getResultsWill,
            $fromDate,
            $toDate
        );

        $database->expects($this->exactly($expectGetUsersTable))
            ->method('getUsersTable')
            ->will($this->returnValue('usersTable'));

        $database->expects($this->exactly($expectGetCapabilitiesTable))
            ->method('getCapabilitiesTable')
            ->will($this->returnValue('capabilitiesTable'));

        /**
         * @var \stdClass $firstUser
         */
        $firstUser = $this->getMockBuilder('\WP_User')->getMock();
        $firstUser->capabilitiesTable = [1 => 1, 2 => 2];

        /**
         * @var \stdClass $secondUser
         */
        $secondUser = $this->getMockBuilder('\WP_User')->getMock();
        $secondUser->capabilitiesTable = 'invalid';

        /**
         * @var \stdClass $thirdUser
         */
        $thirdUser = $this->getMockBuilder('\WP_User')->getMock();
        $thirdUser->capabilitiesTable = [1 => 1];

        /**
         * @var \stdClass $fourthUser
         */
        $fourthUser = $this->getMockBuilder('\WP_User')->getMock();
        $fourthUser->capabilitiesTable = [];

        $objectHandler = $this->getObjectHandler();
        $objectHandler->expects($this->exactly($expectGetUser))
            ->method('getUser')
            ->will($this->returnCallback(
                function ($userId) use (
                    $firstUser,
                    $secondUser,
                    $thirdUser,
                    $fourthUser
                ) {
                    if ($userId === 1) {
                        return $firstUser;
                    } elseif ($userId === 2) {
                        return $secondUser;
                    } elseif ($userId === 3) {
                        return $thirdUser;
                    } elseif ($userId === 4) {
                        return $fourthUser;
                    }

                    return false;
                }
            ));

        $abstractUserGroup = $this->getStub(
            $php,
            $this->getWordpress(),
            $database,
            $this->getMainConfig(),
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isObjectRecursiveMember()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isUserMember()
     *
     * @return AbstractUserGroup
     */
    public function testIsUserMember()
    {
        $abstractUserGroup = $this->getTestIsUserMemberPrototype(
            ['role' => 3, 'user' => 2],
            [],
            [],
            [
                [0, 2, ObjectHandler::GENERAL_ROLE_OBJECT_TYPE],
                [0, 1, ObjectHandler::GENERAL_ROLE_OBJECT_TYPE]
            ],
            0,
            5,
            6,
            'fromDate',
            'toDate'
        );
        $recursiveMembership = [];

        self::setValue($abstractUserGroup, 'assignedObjects', [ObjectHandler::GENERAL_USER_OBJECT_TYPE => []]);
        $return = $abstractUserGroup->isUserMember(4, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);
        self::setValue($abstractUserGroup, 'assignedObjects', [
            ObjectHandler::GENERAL_USER_OBJECT_TYPE => [],
            ObjectHandler::GENERAL_ROLE_OBJECT_TYPE => []
        ]);
        $return = $abstractUserGroup->isUserMember(3, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);
        self::setValue($abstractUserGroup, 'userMembership', []);
        self::setValue($abstractUserGroup, 'assignedObjects', []);

        $return = $abstractUserGroup->isUserMember(1, $recursiveMembership, $fromDate, $toDate);
        self::assertEquals('fromDate', $fromDate);
        self::assertEquals('toDate', $toDate);
        self::assertTrue($return);
        self::assertEquals(
            [
                ObjectHandler::GENERAL_ROLE_OBJECT_TYPE => [
                    1 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE, 'fromDate', 'toDate'),
                    2 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE, 'fromDate', 'toDate')
                ]
            ],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isUserMember(2, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isUserMember(3, $recursiveMembership, $fromDate, $toDate);
        self::assertEquals('fromDate', $fromDate);
        self::assertEquals('toDate', $toDate);
        self::assertTrue($return);
        self::assertEquals([
            ObjectHandler::GENERAL_ROLE_OBJECT_TYPE => [
                1 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE, 'fromDate', 'toDate')
            ]
        ], $recursiveMembership);

        $return = $abstractUserGroup->isUserMember(5, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        return $abstractUserGroup;
    }

    /**
     * Prototype function for the testIsTermMember
     *
     * @return AbstractUserGroup
     */
    private function getTestIsTermMemberPrototype()
    {
        $database = $this->getDatabaseMockForMemberTests(['term' => 3]);

        $objectHandler = $this->getObjectHandler();
        $objectHandler->expects($this->exactly(4))
            ->method('getTermTreeMap')
            ->will($this->returnValue([
                ObjectHandler::TREE_MAP_PARENTS => [
                    ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [
                        1 => [3 => 'term'],
                        2 => [3 => 'term'],
                        4 => [1 => 'term']
                    ]
                ],
                ObjectHandler::TREE_MAP_CHILDREN => [
                    ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [
                        3 => [1 => 'term', 2 => 'term'],
                        1 => [4 => 'term']
                    ]
                ]
            ]));

        $objectHandler->expects($this->any())
            ->method('isTaxonomy')
            ->will($this->returnCallback(function ($objectType) {
                return ($objectType === 'termObjectType');
            }));

        $config = $this->getMainConfig();
        $config->expects($this->exactly(5))
            ->method('lockRecursive')
            ->will($this->onConsecutiveCalls(false, true, true, true, true));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $config,
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isObjectRecursiveMember()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isTermMember()
     *
     * @return AbstractUserGroup
     */
    public function testIsTermMember()
    {
        $abstractUserGroup = $this->getTestIsTermMemberPrototype();
        $recursiveMembership = [];

        // term tests
        $return = $abstractUserGroup->isTermMember(1, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isTermMember(2, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            [ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isTermMember(3, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isTermMember(4, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            [ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [1 => $this->getAssignmentInformation('term')]],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isTermMember(5, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        return $abstractUserGroup;
    }

    /**
     * Prototype function for the testIsPostMember
     *
     * @return AbstractUserGroup
     */
    private function getTestIsPostMemberPrototype()
    {
        $database = $this->getDatabaseMockForMemberTests(['post' => 3, 'term' => 3]);
        $config = $this->getMainConfig();

        $lockRecursiveReturns = [false, true, true, true, true, false];

        $config->expects($this->any())
            ->method('lockRecursive')
            ->will($this->returnCallback(function () use (&$lockRecursiveReturns) {
                if (count($lockRecursiveReturns) > 0) {
                    return array_shift($lockRecursiveReturns);
                }

                return true;
            }));

        $objectHandler = $this->getObjectHandler();
        $objectHandler->expects($this->any())
            ->method('getTermTreeMap')
            ->will($this->returnValue([
                ObjectHandler::TREE_MAP_PARENTS => [
                    ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [
                        1 => [3 => 'term'],
                        2 => [3 => 'term'],
                        4 => [1 => 'term']
                    ]
                ],
                ObjectHandler::TREE_MAP_CHILDREN => [
                    ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [
                        3 => [1 => 'term', 2 => 'term'],
                        1 => [4 => 'term']
                    ]
                ]
            ]));

        $objectHandler->expects($this->any())
            ->method('isPostType')
            ->will($this->returnCallback(function ($objectType) {
                return ($objectType === 'postObjectType');
            }));

        $objectHandler->expects($this->any())
            ->method('getPostTreeMap')
            ->will($this->returnValue([
                ObjectHandler::TREE_MAP_PARENTS => [
                    ObjectHandler::GENERAL_POST_OBJECT_TYPE => [
                        1 => [3 => 'post'],
                        2 => [3 => 'post'],
                        4 => [1 => 'post']
                    ]
                ],
                ObjectHandler::TREE_MAP_CHILDREN => [
                    ObjectHandler::GENERAL_POST_OBJECT_TYPE => [
                        3 => [1 => 'post', 2 => 'post'],
                        1 => [4 => 'post']
                    ]
                ]
            ]));

        $objectHandler->expects($this->any())
            ->method('getPostTermMap')
            ->will($this->returnValue([
                2 => [3 => 'term', 9 => 'term'],
                10 => [3 => 'term']
            ]));

        $objectHandler->expects($this->any())
            ->method('getTermPostMap')
            ->will($this->returnValue([
                2 => [9 => 'post']
            ]));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $config,
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isObjectRecursiveMember()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isPostMember()
     *
     * @return AbstractUserGroup
     */
    public function testIsPostMember()
    {
        $abstractUserGroup = $this->getTestIsPostMemberPrototype();
        $recursiveMembership = [];

        // post tests
        $return = $abstractUserGroup->isPostMember(1, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isPostMember(2, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            [
                ObjectHandler::GENERAL_POST_OBJECT_TYPE => [3 => $this->getAssignmentInformation('post')],
                ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]
            ],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isPostMember(3, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isPostMember(4, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            [ObjectHandler::GENERAL_POST_OBJECT_TYPE => [1 => $this->getAssignmentInformation('post')]],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isPostMember(5, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isPostMember(10, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            [ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]],
            $recursiveMembership
        );

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isObjectRecursiveMember()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::isPluggableObjectMember()
     *
     * @return AbstractUserGroup
     */
    public function testIsPluggableObjectMember()
    {
        $database = $this->getDatabaseMockForMemberTests(['pluggableObject' => 2]);

        $objectHandler = $this->getObjectHandler();
        $objectHandler->expects($this->any())
            ->method('getPluggableObject')
            ->will($this->returnCallback(
                function ($objectType) {
                    if ($objectType === '_pluggableObject_') {
                        $pluggableObject = $this->getMockForAbstractClass(
                            '\UserAccessManager\ObjectHandler\PluggableObject',
                            [],
                            '',
                            false
                        );

                        $pluggableObject->expects($this->any())
                            ->method('getRecursiveMembership')
                            ->will($this->returnCallback(
                                function ($abstractUserGroup, $objectId) {
                                    return ($objectId === 1 || $objectId === 4) ?
                                        ['pluggableObject' =>
                                            [1 => $this->getAssignmentInformation('pluggableObject')]
                                        ] : [];
                                }
                            ));

                        $pluggableObject->expects($this->any())
                            ->method('getFullObjects')
                            ->will($this->returnValue([1 => 'pluggableObject', 6 => 'pluggableObject']));

                        return $pluggableObject;
                    }

                    return null;
                }
            ));

        $objectHandler->expects($this->any())
            ->method('isPluggableObject')
            ->will($this->returnCallback(function ($objectType) {
                return ($objectType === '_pluggableObject_');
            }));

        $abstractUserGroup = $this->getStub(
            $this->getPhp(),
            $this->getWordpress(),
            $database,
            $config = $this->getMainConfig(),
            $this->getUtil(),
            $objectHandler,
            $this->getAssignmentInformationFactory()
        );

        self::setValue($abstractUserGroup, 'id', 123);
        $recursiveMembership = [];

        // pluggable object tests
        $return = $abstractUserGroup->isPluggableObjectMember('noPluggableObject', 1, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isPluggableObjectMember('_pluggableObject_', 1, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            ['pluggableObject' => [1 => $this->getAssignmentInformation('pluggableObject')]],
            $recursiveMembership
        );

        $return = $abstractUserGroup->isPluggableObjectMember('_pluggableObject_', 2, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals([], $recursiveMembership);

        self::assertAttributeEquals(
            [
                'noPluggableObject' => [1 => false],
                '_pluggableObject_' => [
                    1 => ['pluggableObject' => [1 => $this->getAssignmentInformation('pluggableObject')]],
                    2 => []
                ]
            ],
            'pluggableObjectMembership',
            $abstractUserGroup
        );

        $return = $abstractUserGroup->isPluggableObjectMember('_pluggableObject_', 3, $recursiveMembership);
        self::assertFalse($return);
        self::assertEquals([], $recursiveMembership);

        $return = $abstractUserGroup->isPluggableObjectMember('_pluggableObject_', 4, $recursiveMembership);
        self::assertTrue($return);
        self::assertEquals(
            ['pluggableObject' => [1 => $this->getAssignmentInformation('pluggableObject')]],
            $recursiveMembership
        );

        return $abstractUserGroup;
    }

    /**
     * Assertion helper for testIsMemberFunctions
     *
     * @param AbstractUserGroup $abstractUserGroup
     * @param bool              $expectedReturn
     * @param array             $expectedRecursiveMembership
     * @param string            $objectType
     * @param string            $objectId
     */
    private function memberFunctionAssertions(
        AbstractUserGroup $abstractUserGroup,
        $expectedReturn,
        array $expectedRecursiveMembership,
        $objectType,
        $objectId
    ) {
        $recursiveMembership = [];
        $return = $abstractUserGroup->isObjectMember($objectType, $objectId, $recursiveMembership);

        self::assertEquals($expectedReturn, $return);
        self::assertEquals($expectedRecursiveMembership, $recursiveMembership);

        self::assertEquals(
            $expectedRecursiveMembership,
            $abstractUserGroup->getRecursiveMembershipForObject(
                $objectType,
                $objectId
            )
        );

        self::assertEquals(
            count($expectedRecursiveMembership) > 0,
            $abstractUserGroup->isLockedRecursive($objectType, $objectId)
        );
    }

    /**
     * @group   unit
     * @depends testIsRoleMember
     * @depends testIsUserMember
     * @depends testIsTermMember
     * @depends testIsPostMember
     * @depends testIsPluggableObjectMember
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::isObjectMember()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::getRecursiveMembershipForObject()
     * @covers  \UserAccessManager\UserGroup\AbstractUserGroup::isLockedRecursive()
     *
     * @param AbstractUserGroup $roleUserGroup
     * @param AbstractUserGroup $userUserGroup
     * @param AbstractUserGroup $termUserGroup
     * @param AbstractUserGroup $postUserGroup
     * @param AbstractUserGroup $pluggableObjectUserGroup
     */
    public function testIsMemberFunctions(
        AbstractUserGroup $roleUserGroup,
        AbstractUserGroup $userUserGroup,
        AbstractUserGroup $termUserGroup,
        AbstractUserGroup $postUserGroup,
        AbstractUserGroup $pluggableObjectUserGroup
    ) {
        // role tests
        $this->memberFunctionAssertions($roleUserGroup, true, [], ObjectHandler::GENERAL_ROLE_OBJECT_TYPE, 1);
        $this->memberFunctionAssertions($roleUserGroup, false, [], ObjectHandler::GENERAL_ROLE_OBJECT_TYPE, 4);

        // user tests
        $this->memberFunctionAssertions(
            $userUserGroup,
            true,
            [
                ObjectHandler::GENERAL_ROLE_OBJECT_TYPE => [
                    1 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE),
                    2 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE)
                ]
            ],
            ObjectHandler::GENERAL_USER_OBJECT_TYPE,
            1
        );
        $this->memberFunctionAssertions($userUserGroup, true, [], ObjectHandler::GENERAL_USER_OBJECT_TYPE, 2);
        $this->memberFunctionAssertions(
            $userUserGroup,
            true,
            [
                ObjectHandler::GENERAL_ROLE_OBJECT_TYPE => [
                    1 => $this->getAssignmentInformation(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE)
                ]
            ],
            ObjectHandler::GENERAL_USER_OBJECT_TYPE,
            3
        );
        $this->memberFunctionAssertions($userUserGroup, false, [], ObjectHandler::GENERAL_USER_OBJECT_TYPE, 5);

        // term tests
        $this->memberFunctionAssertions($termUserGroup, true, [], ObjectHandler::GENERAL_TERM_OBJECT_TYPE, 1);
        $this->memberFunctionAssertions(
            $termUserGroup,
            true,
            [ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]],
            'termObjectType',
            2
        );
        $this->memberFunctionAssertions($termUserGroup, true, [], ObjectHandler::GENERAL_TERM_OBJECT_TYPE, 3);
        $this->memberFunctionAssertions(
            $termUserGroup,
            true,
            [ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [1 => $this->getAssignmentInformation('term')]],
            ObjectHandler::GENERAL_TERM_OBJECT_TYPE,
            4
        );
        $this->memberFunctionAssertions($termUserGroup, false, [], ObjectHandler::GENERAL_TERM_OBJECT_TYPE, 5);

        // post tests
        $this->memberFunctionAssertions($postUserGroup, true, [], ObjectHandler::GENERAL_POST_OBJECT_TYPE, 1);
        $this->memberFunctionAssertions(
            $postUserGroup,
            true,
            [
                ObjectHandler::GENERAL_POST_OBJECT_TYPE => [3 => $this->getAssignmentInformation('post')],
                ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]
            ],
            'postObjectType',
            2
        );
        $this->memberFunctionAssertions(
            $postUserGroup,
            true,
            [
                ObjectHandler::GENERAL_POST_OBJECT_TYPE => [3 => $this->getAssignmentInformation('post')],
                ObjectHandler::GENERAL_TERM_OBJECT_TYPE => [3 => $this->getAssignmentInformation('term')]
            ],
            ObjectHandler::GENERAL_POST_OBJECT_TYPE,
            2
        );
        $this->memberFunctionAssertions($postUserGroup, true, [], ObjectHandler::GENERAL_POST_OBJECT_TYPE, 3);
        $this->memberFunctionAssertions(
            $postUserGroup,
            true,
            [ObjectHandler::GENERAL_POST_OBJECT_TYPE => [1 => $this->getAssignmentInformation('post')]],
            ObjectHandler::GENERAL_POST_OBJECT_TYPE,
            4
        );
        $this->memberFunctionAssertions($postUserGroup, false, [], ObjectHandler::GENERAL_POST_OBJECT_TYPE, 5);

        // pluggable object tests
        $this->memberFunctionAssertions($pluggableObjectUserGroup, false, [], 'noPluggableObject', 1);
        $this->memberFunctionAssertions(
            $pluggableObjectUserGroup,
            true,
            ['pluggableObject' => [1 => $this->getAssignmentInformation('pluggableObject')]],
            '_pluggableObject_',
            1
        );
        $this->memberFunctionAssertions($pluggableObjectUserGroup, false, [], '_pluggableObject_', 3);
    }

    /**
     * Generates return values.
     *
     * @param array $numbers
     *
     * @return array
     */
    private function generateUserReturn(array $numbers)
    {
        $returns = [];

        foreach ($numbers as $number) {
            $return = new \stdClass();
            $return->ID = $number;
            $returns[] = $return;
        }

        return $returns;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getFullUsers()
     *
     * @return AbstractUserGroup
     */
    public function testGetFullUser()
    {
        $query = "SELECT ID, user_nicename FROM usersTable";

        $abstractUserGroup = $this->getTestIsUserMemberPrototype(
            ['user' => 2, 'role' => 3],
            [[new MatchIgnoreWhitespace($query)]],
            [$this->generateUserReturn([10 => 10, 1, 2, 3])],
            [
                [0, 2, ObjectHandler::GENERAL_ROLE_OBJECT_TYPE],
                [0, 1, ObjectHandler::GENERAL_ROLE_OBJECT_TYPE]
            ],
            1,
            3,
            4
        );

        self::assertEquals(
            [
                1 => ObjectHandler::GENERAL_USER_OBJECT_TYPE,
                2 => ObjectHandler::GENERAL_USER_OBJECT_TYPE,
                3 => ObjectHandler::GENERAL_USER_OBJECT_TYPE
            ],
            $abstractUserGroup->getFullUsers()
        );

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getFullTerms()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getFullObjects()
     *
     * @return AbstractUserGroup
     */
    public function testGetFullTerms()
    {
        $abstractUserGroup = $this->getTestIsTermMemberPrototype();
        self::assertEquals(
            [1 => 'term', 2 => 'term', 3 => 'term'],
            $abstractUserGroup->getFullTerms()
        );

        self::setValue($abstractUserGroup, 'fullObjectMembership', []);
        self::assertEquals(
            [1 => 'term', 2 => 'term', 3 => 'term', 4 => 'term'],
            $abstractUserGroup->getFullTerms()
        );

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getFullPosts()
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getFullObjects()
     *
     * @return AbstractUserGroup
     */
    public function testGetFullPosts()
    {
        $abstractUserGroup = $this->getTestIsPostMemberPrototype();
        self::assertEquals(
            [1 => 'post', 2 => 'post', 3 => 'post', 9 => 'post'],
            $abstractUserGroup->getFullPosts()
        );

        self::setValue($abstractUserGroup, 'fullObjectMembership', []);
        self::assertEquals(
            [1 => 'post', 2 => 'post', 3 => 'post', 4 => 'post', 9 => 'post'],
            $abstractUserGroup->getFullPosts()
        );

        return $abstractUserGroup;
    }

    /**
     * @group  unit
     * @depends testIsRoleMember
     * @depends testGetFullUser
     * @depends testGetFullTerms
     * @depends testGetFullPosts
     * @depends testIsPluggableObjectMember
     * @covers \UserAccessManager\UserGroup\AbstractUserGroup::getAssignedObjectsByType()
     *
     * @param AbstractUserGroup $roleUserGroup
     * @param AbstractUserGroup $userUserGroup
     * @param AbstractUserGroup $termUserGroup
     * @param AbstractUserGroup $postUserGroup
     * @param AbstractUserGroup $pluggableObjectUserGroup
     */
    public function testGetAssignedObjectsByType(
        AbstractUserGroup $roleUserGroup,
        AbstractUserGroup $userUserGroup,
        AbstractUserGroup $termUserGroup,
        AbstractUserGroup $postUserGroup,
        AbstractUserGroup $pluggableObjectUserGroup
    ) {
        self::assertEquals(
            [1 => 'role', 2 => 'role', 3 => 'role'],
            $roleUserGroup->getAssignedObjectsByType(ObjectHandler::GENERAL_ROLE_OBJECT_TYPE)
        );

        self::assertEquals(
            [
                1 => ObjectHandler::GENERAL_USER_OBJECT_TYPE,
                2 => ObjectHandler::GENERAL_USER_OBJECT_TYPE,
                3 => ObjectHandler::GENERAL_USER_OBJECT_TYPE
            ],
            $userUserGroup->getAssignedObjectsByType(ObjectHandler::GENERAL_USER_OBJECT_TYPE)
        );

        self::assertEquals(
            [1 => 'term', 2 => 'term', 3 => 'term', 4 => 'term'],
            $termUserGroup->getAssignedObjectsByType(ObjectHandler::GENERAL_TERM_OBJECT_TYPE)
        );
        self::setValue($termUserGroup, 'fullObjectMembership', ['termObjectType' => [1 => 'term', 2 => 'term']]);
        self::assertEquals(
            [1 => 'term', 2 => 'term'],
            $termUserGroup->getAssignedObjectsByType('termObjectType')
        );

        self::assertEquals(
            [1 => 'post', 2 => 'post', 3 => 'post', 4 => 'post', 9 => 'post'],
            $postUserGroup->getAssignedObjectsByType(ObjectHandler::GENERAL_POST_OBJECT_TYPE)
        );
        self::setValue($postUserGroup, 'fullObjectMembership', ['postObjectType' => [3 => 'post', 4 => 'post']]);
        self::assertEquals(
            [3 => 'post', 4 => 'post'],
            $postUserGroup->getAssignedObjectsByType('postObjectType')
        );

        self::assertEquals(
            [1 => 'pluggableObject', 6 => 'pluggableObject'],
            $pluggableObjectUserGroup->getAssignedObjectsByType('_pluggableObject_')
        );

        self::assertEquals(
            [],
            $pluggableObjectUserGroup->getAssignedObjectsByType('nothing')
        );
    }
}