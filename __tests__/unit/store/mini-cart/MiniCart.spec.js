import { shallowMount } from '@vue/test-utils';
import MiniCart from '@/js/components/store/MiniCart';
import VueMoment from 'vue-moment';
import Vue from 'vue';
import orderApi from '@/js/api/order/order';
import productsWithoutVariantState from './mocks/products-without-variant';
import productsWithVariantState from './mocks/products-with-variant';
import productsWithMultipleQuantitiesState from './mocks/products-with-multiple-quantities';
import defaultMockState from './mocks/default';
import updateItemSuccessResponse from '../../mocks/api/order/update_item/success';
import deleteItemSuccessResponse from '../../mocks/api/order/delete_item/success';
import '@/js/filters/Currency';
import '@/js/filters/Capitalize';

Vue.use(VueMoment);

beforeEach(() => {
  jest.clearAllMocks();
});

describe('MiniCart', () => {
  const createWrapper = (state) => shallowMount(MiniCart, {
    propsData: { merchant: { delivery_cost: 599 } },
    mocks: state,
  });

  it('is inactive when the cart is empty', () => {
    const wrapper = createWrapper(defaultMockState);
    expect(wrapper.find('.cart--empty').exists()).toBe(true);
  });

  it('shows item name', async () => {
    const wrapper = createWrapper(productsWithVariantState);

    expect(wrapper.find('.cart__title').text()).toContain('Nachos,Cheese & Chilli');
  });

  it('shows cart options', async () => {
    const wrapper = createWrapper(productsWithVariantState);
    expect(wrapper.find('.cart__options--option').text()).toContain('Option 1');
  });

  it('shows item variant', async () => {
    const wrapper = createWrapper(productsWithVariantState);

    expect(wrapper.find('.cart__options--variant').text()).toContain('Variant');
  });

  it('shows the totals', async () => {
    const wrapper = createWrapper(productsWithVariantState);

    expect(wrapper.find('.subtotal').text()).toContain('£5.00');
    expect(wrapper.find('.total').text()).toContain('£10.99');
  });

  it('increments quantity', async () => {
    const wrapper = createWrapper(productsWithVariantState);
    const spy = jest.spyOn(orderApi, 'updateItem')
      .mockImplementation(updateItemSuccessResponse);

    await wrapper.find('.increment__type--up').trigger('click');

    expect(spy).toBeCalled();
    expect(productsWithVariantState.$store.dispatch)
      .toHaveBeenCalledWith(
        'cart/updateProduct',
        updateItemSuccessResponse().data,
      );
  });

  it('decrements item when decrementing quantity of greater than 1', async () => {
    const wrapper = createWrapper(productsWithMultipleQuantitiesState);
    const spy = jest.spyOn(orderApi, 'updateItem')
      .mockImplementation(updateItemSuccessResponse);

    await wrapper.find('.increment__type--down').trigger('click');

    expect(spy).toBeCalled();
    expect(productsWithMultipleQuantitiesState.$store.dispatch)
      .toHaveBeenCalledWith(
        'cart/updateProduct',
        updateItemSuccessResponse().data,
      );
  });

  it('removes item when decrementing quantity of 1', async () => {
    const wrapper = createWrapper(productsWithVariantState);
    const spy = jest.spyOn(orderApi, 'deleteItem')
      .mockImplementation(deleteItemSuccessResponse);

    await wrapper.find('.increment__type--down').trigger('click');

    expect(spy).toBeCalled();
    expect(productsWithVariantState.$store.dispatch)
      .toHaveBeenCalledWith(
        'cart/removeFromCart',
        '1596187347277',
      );
  });

  it('formats cart service content', () => {
    const wrapper = createWrapper(productsWithVariantState);
    expect(wrapper.find('.cart-service__content').text()).toContain('Delivery, 1st Jan, 00:00');
  });
});
