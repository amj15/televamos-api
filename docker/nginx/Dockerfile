FROM nginx:alpine

# Remove the default.conf directory if it exists
RUN rm -rf /etc/nginx/conf.d/default.conf

# Copy the custom default.conf
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf