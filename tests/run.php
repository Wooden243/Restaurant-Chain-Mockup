<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

use RestaurantMockup\Contracts\FileConvertible;
use RestaurantMockup\Models\Company;
use RestaurantMockup\Models\Employee;
use RestaurantMockup\Models\RestaurantChain;
use RestaurantMockup\Models\RestaurantLocation;
use RestaurantMockup\Models\User;
use RestaurantMockup\Support\Html;
use RestaurantMockup\Support\RandomData;

$passed = 0;
$failed = 0;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function rejects(callable $callback): bool
{
    try {
        $callback();
        return false;
    } catch (InvalidArgumentException) {
        return true;
    }
}

function runTest(string $title, callable $test): void
{
    global $passed, $failed;
    try {
        $test();
        $passed++;
        echo "PASS {$title}\n";
    } catch (Throwable $e) {
        $failed++;
        fwrite(STDERR, "FAIL {$title}: {$e->getMessage()}\n");
    }
}

$user = new User(1, 'Jane Doe', 'jane@example.com');
$employee = new Employee(2, 'Alex Worker', 'alex@example.com', 'Chef', 52000.5, new DateTimeImmutable('2023-05-21'), ['Chef', 'Trainer']);
$company = new Company('Sunrise Co', 1998, 'Local dining.', 'https://example.com', '(555) 555-1234', 'Food', 'Morgan', true);
$location = new RestaurantLocation('Main Street', '123 Oak Ave', 'Boston', 'MA', '02134', true);
$chain = new RestaurantChain('Sunrise Co', 1998, 'Local dining.', 'https://example.com', '(555) 555-1234', 'Food', 'Morgan', true, 102, 'Italian', true, 'Independent');

runTest('All five domain classes implement FileConvertible', static function (): void {
    foreach ([User::class, Employee::class, Company::class, RestaurantLocation::class, RestaurantChain::class] as $class) {
        check(is_subclass_of($class, FileConvertible::class), "Missing FileConvertible in {$class}");
    }
});
runTest('Interface declares all three conversion methods', static function (): void {
    foreach (['toString','toHTML','toMarkdown'] as $method) {
        check(method_exists(FileConvertible::class, $method), "Missing {$method}");
    }
});
runTest('All domain classes declare a static RandomGenerator()', static function (): void {
    foreach ([User::class, Employee::class, Company::class, RestaurantLocation::class, RestaurantChain::class] as $class) {
        check((new ReflectionMethod($class, 'RandomGenerator'))->isStatic(), "Non-static generator in {$class}");
    }
});
runTest('Employee inherits from User', static fn() => check(is_subclass_of(Employee::class, User::class), 'Employee does not extend User'));
runTest('RestaurantChain inherits from Company', static fn() => check(is_subclass_of(RestaurantChain::class, Company::class), 'RestaurantChain does not extend Company'));
runTest('User exposes its inherited identity data', static function () use ($user): void {
    check($user->getId() === 1 && $user->getName() === 'Jane Doe' && $user->getEmail() === 'jane@example.com', 'Wrong user fields');
});
runTest('User string includes ID/name/email', static fn() => check(str_contains($user->toString(), 'jane@example.com'), 'Missing email'));
runTest('User renders HTML', static fn() => check(str_contains($user->toHTML(), 'Jane Doe'), 'Missing name'));
runTest('User renders Markdown', static fn() => check(str_contains($user->toMarkdown(), '- User #1'), 'Wrong markdown'));
runTest('User rejects invalid identity', static fn() => check(rejects(static fn() => new User(0, 'Test', 'test@example.com')), 'Accepted ID 0'));
runTest('User rejects invalid email', static fn() => check(rejects(static fn() => new User(3, 'Test', 'not-an-email')), 'Accepted invalid email'));
runTest('Employee getters include job/salary/date/roles', static function () use ($employee): void {
    check($employee->getJobTitle() === 'Chef' && $employee->getSalary() === 52000.5 && $employee->getStartDate()->format('Y-m-d') === '2023-05-21' && count($employee->getRoles()) === 2, 'Wrong employee fields');
});
runTest('Employee string includes all required fields', static function () use ($employee): void {
    foreach (['Chef','52,000.50','2023-05-21','Trainer'] as $required) {
        check(str_contains($employee->toString(), $required), "Missing {$required}");
    }
});
runTest('Employee HTML uses list item', static fn() => check(str_starts_with($employee->toHTML(), '<li'), 'Expected <li>'));
runTest('Employee Markdown lists employee', static fn() => check(str_contains($employee->toMarkdown(), 'Alex Worker'), 'Missing employee'));
runTest('Employee rejects negative salary', static fn() => check(rejects(static fn() => new Employee(8, 'Test', 'test@example.com', 'Chef', -2, new DateTimeImmutable(), ['Cook'])), 'Accepted negative salary'));
runTest('Employee rejects non-finite salary', static fn() => check(rejects(static fn() => new Employee(8, 'Test', 'test@example.com', 'Chef', INF, new DateTimeImmutable(), ['Cook'])), 'Accepted INF salary'));
runTest('Employee rejects empty role', static fn() => check(rejects(static fn() => new Employee(8, 'Test', 'test@example.com', 'Chef', 1, new DateTimeImmutable(), [''])), 'Accepted blank role'));
runTest('Company getter values match input', static function () use ($company): void {
    check($company->getName() === 'Sunrise Co' && $company->getYearFounded() === 1998 && $company->getDescription() === 'Local dining.' && $company->getWebsite() === 'https://example.com' && $company->getPhone() === '(555) 555-1234' && $company->getIndustry() === 'Food' && $company->getCeo() === 'Morgan' && $company->isPubliclyTraded(), 'Wrong company fields');
});
runTest('Company text includes founding and CEO', static fn() => check(str_contains($company->toString(), 'Founded: 1998') && str_contains($company->toString(), 'CEO: Morgan'), 'Incomplete company output'));
runTest('Company HTML links safely and displays status', static fn() => check(str_contains($company->toHTML(), 'rel="noopener noreferrer"') && str_contains($company->toHTML(), 'Yes'), 'Incomplete company HTML'));
runTest('Company Markdown lists required fields', static fn() => check(str_contains($company->toMarkdown(), '**Publicly Traded**: Yes') && str_contains($company->toMarkdown(), '**Phone**:'), 'Incomplete markdown'));
runTest('Company rejects javascript URL', static fn() => check(rejects(static fn() => new Company('Co', 2000, '', 'javascript:alert(1)', '', '', '', false)), 'Accepted unsafe URL'));
runTest('Company rejects future founding year', static fn() => check(rejects(static fn() => new Company('Co', (int) date('Y') + 1, '', 'https://example.com', '', '', '', false)), 'Accepted future year'));
runTest('Location exposes all location fields', static function () use ($location): void {
    check($location->getName() === 'Main Street' && $location->getAddress() === '123 Oak Ave' && $location->getCity() === 'Boston' && $location->getState() === 'MA' && $location->getPostalCode() === '02134' && $location->isOpen(), 'Incorrect location fields');
});
runTest('Location initially has zero employees', static fn() => check(count($location->getEmployees()) === 0, 'Unexpected employees'));
runTest('Location can add an employee', static function () use ($location, $employee): void {
    $location->addEmployee($employee);
    check(count($location->getEmployees()) === 1, 'Employee was not appended');
});
runTest('Location rejects duplicate employee ID', static function () use ($location, $employee): void {
    check(rejects(static fn() => $location->addEmployee($employee)), 'Duplicate was accepted');
});
runTest('Location toString contains address and employee', static fn() => check(str_contains($location->toString(), 'Boston') && str_contains($location->toString(), 'Alex Worker'), 'Incorrect text'));
runTest('Location HTML has native accordion and employee list', static fn() => check(str_contains($location->toHTML(), '<details') && str_contains($location->toHTML(), '<summary>') && str_contains($location->toHTML(), 'Alex Worker'), 'Missing accordion/employee'));
runTest('Location Markdown includes ZIP and employee', static fn() => check(str_contains($location->toMarkdown(), '02134') && str_contains($location->toMarkdown(), 'Trainer'), 'Incomplete markdown'));
runTest('Location rejects blank address', static fn() => check(rejects(static fn() => new RestaurantLocation('Main', '', 'Boston', 'MA', '02134', true)), 'Accepted blank address'));
runTest('Chain exposes its own and inherited fields', static function () use ($chain): void {
    check($chain->getChainId() === 102 && $chain->getCuisineType() === 'Italian' && $chain->hasDriveThru() && $chain->getParentCompany() === 'Independent' && $chain->getYearFounded() === 1998, 'Incorrect chain values');
});
runTest('Chain location count starts at zero', static fn() => check($chain->getNumberOfLocations() === 0, 'Unexpected location count'));
runTest('Chain addLocation updates its count', static function () use ($chain, $location): void {
    $chain->addLocation($location);
    check($chain->getNumberOfLocations() === 1 && $chain->getRestaurantLocations()[0] === $location, 'Location not added');
});
runTest('Chain toString includes nested location/employee/company', static function () use ($chain): void {
    $s = $chain->toString();
    check(str_contains($s, 'Sunrise Co') && str_contains($s, 'Main Street') && str_contains($s, 'Alex Worker'), 'Missing nested text');
});
runTest('Chain HTML includes nested details and company information', static function () use ($chain): void {
    $h = $chain->toHTML();
    check(str_contains($h, '<section') && str_contains($h, '<details class="location"') && str_contains($h, '<details class="company-details"') && str_contains($h, 'Sunrise Co'), 'Missing nested HTML');
});
runTest('Chain Markdown includes nested content', static function () use ($chain): void {
    $m = $chain->toMarkdown();
    check(str_contains($m, '## Restaurant Chain') && str_contains($m, '### Main Street') && str_contains($m, 'Company Details'), 'Missing markdown');
});
runTest('Chain rejects zero chain ID', static fn() => check(rejects(static fn() => new RestaurantChain('Co', 2000, '', 'https://example.com', '', '', '', false, 0, 'Italian', true, 'Independent')), 'Accepted zero chain ID'));
runTest('HTML conversion escapes untrusted content', static function (): void {
    $loc = new RestaurantLocation('<script>alert(1)</script>', '1 & 2 street', 'Boston', 'MA', '01234', true);
    check(str_contains($loc->toHTML(), '&lt;script&gt;') && !str_contains($loc->toHTML(), '<script>') && str_contains($loc->toHTML(), '1 &amp; 2'), 'HTML injection not escaped');
});
runTest('Markdown pipe and backslash are escaped', static fn() => check(Html::markdown('A|B\\C') === 'A\\|B\\\\C', 'Markdown escaping incorrect'));
runTest('RandomData pick rejects empty array', static fn() => check(rejects(static fn() => RandomData::pick([])), 'Empty array accepted'));
runTest('All random generators return the correct type', static function (): void {
    foreach ([User::class, Employee::class, Company::class, RestaurantLocation::class, RestaurantChain::class] as $class) {
        check($class::RandomGenerator() instanceof $class, "Wrong random type for {$class}");
    }
});
runTest('Generated restaurant chains contain 2–4 locations', static function (): void {
    for ($i = 0; $i < 30; $i++) {
        $generated = RestaurantChain::RandomGenerator();
        check($generated->getNumberOfLocations() >= 2 && $generated->getNumberOfLocations() <= 4, 'Wrong random location count');
    }
});
runTest('Generated restaurant locations contain 2–5 employees', static function (): void {
    for ($i = 0; $i < 30; $i++) {
        $generated = RestaurantLocation::RandomGenerator();
        check(count($generated->getEmployees()) >= 2 && count($generated->getEmployees()) <= 5, 'Wrong random employee count');
    }
});
runTest('Generated employees have distinct IDs within location', static function (): void {
    for ($i = 0; $i < 40; $i++) {
        $loc = RestaurantLocation::RandomGenerator();
        $ids = array_map(static fn(Employee $e): int => $e->getId(), $loc->getEmployees());
        check(count($ids) === count(array_unique($ids)), 'Duplicate IDs in location');
    }
});
runTest('Generated employee start dates are not in the future', static function (): void {
    for ($i = 0; $i < 40; $i++) {
        check(Employee::RandomGenerator()->getStartDate() <= new DateTimeImmutable('today'), 'Future date generated');
    }
});
runTest('Every generated class produces three nonempty formats', static function (): void {
    foreach ([User::class, Employee::class, Company::class, RestaurantLocation::class, RestaurantChain::class] as $class) {
        $obj = $class::RandomGenerator();
        foreach ([$obj->toString(), $obj->toHTML(), $obj->toMarkdown()] as $result) {
            check(trim($result) !== '', "Empty conversion for {$class}");
        }
    }
});
runTest('Index page renders three chains', static function (): void {
    ob_start();
    require dirname(__DIR__) . '/index.php';
    $html = (string) ob_get_clean();
    check(substr_count($html, '<section class="chain">') === 3, 'Incorrect number of chains');
    check(str_contains($html, '<!doctype html>') && str_contains($html, 'Company Details'), 'Incomplete HTML page');
});
runTest('Two page renders use newly generated mock datasets', static function (): void {
    ob_start(); require dirname(__DIR__) . '/index.php'; $a = (string) ob_get_clean();
    ob_start(); require dirname(__DIR__) . '/index.php'; $b = (string) ob_get_clean();
    check($a !== $b, 'Expected freshly randomized content');
});

$total = $passed + $failed;
echo "\nResult: {$passed}/{$total} passed, {$failed} failed.\n";
exit($failed === 0 ? 0 : 1);
