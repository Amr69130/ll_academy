import { Application } from '@hotwired/stimulus';
import Carousel from '@stimulus-components/carousel';
import 'swiper/css/bundle';

const application = Application.start();
application.register('carousel', Carousel);
