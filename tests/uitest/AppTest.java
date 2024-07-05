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

    private String url = "https://whattheduck.ddns.net/Login.php";  // Update this to your live site URL
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidPassword = "invalid_password";

    @Before
    public void setUp() {
        driver = new HtmlUnitDriver();
        wait = new WebDriverWait(driver, 10);
    }

    @After
    public void tearDown() {
        driver.quit();
    }

    @Test
    public void testLoginWithValidCredentials() {
        driver.get(url);
        wait.until(ExpectedConditions.titleContains("Login"));

        driver.findElement(By.name("cust_username")).sendKeys(validUsername);
        driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
        driver.findElement(By.id("submit")).click();

        // Assuming successful login redirects to dashboard
        assertTrue(wait.until(ExpectedConditions.titleContains("Dashboard")));
    }

    @Test
    public void testLoginWithInvalidCredentials() {
        driver.get(url);
        wait.until(ExpectedConditions.titleContains("Login"));

        driver.findElement(By.name("cust_username")).sendKeys(validUsername);
        driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
        driver.findElement(By.id("submit")).click();

        // Check for login failed message
        String errorMsg = wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title"))).getText();
        assertEquals("Login failed!", errorMsg);
    }
}
