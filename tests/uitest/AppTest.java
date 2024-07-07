import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import static org.junit.Assert.assertTrue;

public class AppTest {

    private WebDriver driver;
    private WebDriverWait wait;
    private String loginUrl = "https://whattheduck.ddns.net/Login.php";
    private String signupUrl = "https://whattheduck.ddns.net/Signup.php";
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidUsername = "invaliduser";
    private String invalidPassword = "invalidPassword";

    @Before
    public void setUp() {
        // Set the path to your ChromeDriver executable
        System.setProperty("webdriver.chrome.driver", "/usr/local/bin/chromedriver");

        // Set ChromeOptions to run Chrome in headless mode
        ChromeOptions options = new ChromeOptions();
        options.addArguments("--headless");
        options.addArguments("--no-sandbox");
        options.addArguments("--disable-dev-shm-usage");

        driver = new ChromeDriver(options);
        wait = new WebDriverWait(driver, 30); // Increased the timeout
    }

    @After
    public void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }

    @Test
    public void testLoginWithValidCredentials() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            driver.get("https://whattheduck.ddns.net/index.php");
            wait.until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[contains(text(),'" + validUsername + "')]")));

            WebElement usernameLink = driver.findElement(By.xpath("//a[contains(text(),'" + validUsername + "')]"));
            assertTrue(usernameLink.isDisplayed());
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithInvalidCredentials() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithValidUsernameAndInvalidPassword() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithInvalidUsernameAndValidPassword() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

   
}
