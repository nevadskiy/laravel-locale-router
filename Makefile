init: build install

build:
	docker build -t app .

install:
	docker run --rm -v ${PWD}:/app app composer install

update:
	docker run --rm -v ${PWD}:/app app composer update

outdated:
	docker run --rm -v ${PWD}:/app app composer outdated

test:
	docker run --rm -it -v ${PWD}:/app app vendor/bin/phpunit
