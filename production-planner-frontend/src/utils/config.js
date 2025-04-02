export const API_BASE_URL = "http://localhost/api";

export const API_ENDPOINTS = {
  getProducibleItems: `${API_BASE_URL}/producible-items`,
  getItem: (itemId) => `${API_BASE_URL}/items/${itemId}`,
  getRecipeForItem: (itemId) => `${API_ENDPOINTS.getItem(itemId)}/standard-recipe`,
  getMachine: (machineId) => `${API_BASE_URL}/machines/${machineId}`,
  login: `${API_BASE_URL}/auth/login`,
  register: `${API_BASE_URL}/auth/register`,
  me: `${API_BASE_URL}/auth/me`
};