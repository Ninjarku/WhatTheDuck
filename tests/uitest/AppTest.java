import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import static org.junit.Assert.*;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class AppTest {
    private WebDriver driver;
    private WebDriverWait wait;

    private String url = "http://whattheduck.ddns.net/Login.php";  // Update this to your live site URL
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidPassword = "invalid_password";

    @Before
    public void setUp() {
        driver = new HtmlUnitDriver();
        wait = new WebDriverWait(driver, 20);  // Increase the wait time to 20 seconds
    }

    @After
    public void tearDown() {
        driver.quit();
    }

    @Test
    public void testLoginWithValidCredentials() {
        driver.get(url);

        // Log the current URL for debugging
        System.out.println("Current URL before login: " + driver.getCurrentUrl());
        System.out.println("Page title: " + driver.getTitle());
        System.out.println("Page source: " + driver.getPageSource());

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.id("cust-login")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of success popup
            System.out.println("Login form submitted.");

            // Wait for the success popup and click "Return to Home"
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//button[text()='Return to Home']")));
            System.out.println("Success popup is visible.");

            driver.findElement(By.xpath("//button[text()='Return to Home']")).click();
            System.out.println("Clicked 'Return to Home'.");

            // Log the current URL and HTML content for debugging
            System.out.println("Current URL after clicking 'Return to Home': " + driver.getCurrentUrl());
            System.out.println("Page source after clicking 'Return to Home': " + driver.getPageSource());

            // Verify that the page redirects to the home page after clicking the button
            assertTrue(wait.until(ExpectedConditions.urlContains("index.php")));
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testLoginWithInvalidCredentials() {
        driver.get(url);

        // Log the current URL for debugging
        System.out.println("Current URL before login: " + driver.getCurrentUrl());
        System.out.println("Page title: " + driver.getTitle());
        System.out.println("Page source: " + driver.getPageSource());

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.id("cust-login")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of error message
            System.out.println("Login form submitted with invalid credentials.");

            // Check for login failed message
            By errorMsgId = By.className("swal2-title");
            
            // Log the current URL and HTML content for debugging
            System.out.println("Current URL after failed login: " + driver.getCurrentUrl());
            System.out.println("Page source after failed login: " + driver.getPageSource());

            String errorMsg = wait.until(ExpectedConditions.visibilityOfElementLocated(errorMsgId)).getText();
            System.out.println("Error message: " + errorMsg);  // Log the error message
            assertEquals("Login failed!", errorMsg);
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }
}
