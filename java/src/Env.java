/**
 * Recovery helper: reads configuration from environment variables.
 * The default value is used when the variable is not set or is empty.
 */
public final class Env {
    private Env() {}

    public static String get(String name, String defaultValue) {
        String value = System.getenv(name);
        return (value == null || value.isEmpty()) ? defaultValue : value;
    }
}
