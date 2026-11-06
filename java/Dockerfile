# Builds the PISID Java processes (MQTT -> MongoDB bridge and MongoDB -> MariaDB migrators).
# Run one class per container: the Compose file sets the class name as the command.
FROM eclipse-temurin:17-jdk AS build
WORKDIR /build
COPY lib/ lib/
COPY src/ src/
RUN mkdir classes \
 && javac -encoding UTF-8 -nowarn -cp "lib/*" -d classes src/*.java

FROM eclipse-temurin:17-jre
WORKDIR /app
COPY --from=build /build/lib/ lib/
COPY --from=build /build/classes/ classes/
ENTRYPOINT ["java", "-cp", "classes:lib/*"]
CMD ["Pc1"]
